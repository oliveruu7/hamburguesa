<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class . ':clientes.index')->only('index');
        $this->middleware(Perm::class . ':clientes.create')->only(['create', 'store']);
        $this->middleware(Perm::class . ':clientes.edit')->only(['edit', 'update']);
        $this->middleware(Perm::class . ':clientes.delete')->only('destroy');
    }

    public function index()
    {
        $clientes = Cliente::where('estado', 'activo')->orderBy('nombre')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['nombre'] = ltrim($data['nombre']);
        $data['ci'] = ltrim($data['ci']);
        $data['telefono'] = isset($data['telefono']) ? ltrim($data['telefono']) : null;

        $validator = Validator::make($data, [
            'nombre'   => ['required', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', 'max:25'],
            'ci'       => ['required', 'digits_between:7,8', 'unique:cliente,ci'],
            'telefono' => ['nullable', 'digits_between:7,8'],
        ], [
            'nombre.required'   => 'El campo nombre es obligatorio.',
            'nombre.regex'      => 'El nombre solo debe contener letras y espacios.',
            'nombre.max'        => 'El nombre no debe exceder los 25 caracteres.',
            'ci.required'       => 'El campo CI es obligatorio.',
            'ci.digits_between'=> 'El CI debe tener entre 7 y 8 dígitos.',
            'ci.unique'         => 'Este CI ya está registrado.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 8 dígitos.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data['estado'] = 'activo';
        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->all();
        $data['nombre'] = ltrim($data['nombre']);
        $data['ci'] = ltrim($data['ci']);
        $data['telefono'] = isset($data['telefono']) ? ltrim($data['telefono']) : null;

        $validator = Validator::make($data, [
            'nombre'   => ['required', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u', 'max:25'],
            'ci'       => ['required', 'digits_between:7,8', 'unique:cliente,ci,' . $cliente->idcliente . ',idcliente'],
            'telefono' => ['nullable', 'digits_between:7,8'],
        ], [
            'nombre.required'   => 'El campo nombre es obligatorio.',
            'nombre.regex'      => 'El nombre solo debe contener letras y espacios.',
            'nombre.max'        => 'El nombre no debe exceder los 25 caracteres.',
            'ci.required'       => 'El campo CI es obligatorio.',
            'ci.digits_between'=> 'El CI debe tener entre 7 y 8 dígitos.',
            'ci.unique'         => 'Este CI ya está registrado por otro cliente.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 8 dígitos.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->update(['estado' => 'inactivo']);
        return back()->with('success', 'Cliente desactivado correctamente.');
    }
}
