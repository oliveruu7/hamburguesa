<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

     public function verify(Request $request)
{
    // Validar los datos del formulario
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|max:50', 
        'password' => 'required|min:6|max:50', 
    ], [
        'email.required' => 'El correo es obligatorio.',
        'email.email' => 'El correo debe ser válido.',
        'email.max' => 'El correo no puede exceder los 50 caracteres.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.max' => 'La contraseña no puede exceder los 50 caracteres.',
    ]);

    // Si la validación falla, redirigir con errores
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Obtener los datos validados
    $email = $request->input('email');
    $password = $request->input('password');

    // Buscar el usuario por email
    $usuario = DB::table('usuario')->where('email', $email)->first();

    // Validar si el usuario existe
    if (!$usuario) {
        return back()->with('error', 'El correo no existe.')->withInput();
    }

    // Validar la contraseña
    if (!password_verify($password, $usuario->password)) {
        return back()->with('error', 'Contraseña incorrecta.')->withInput();
    }

    // Validar si el usuario está activo
    if (isset($usuario->estado) && !$usuario->estado) {
        return back()->with('error', 'Tu cuenta está desactivada. Contacta al administrador.')->withInput();
    }

    // Iniciar sesión manualmente
    Auth::loginUsingId($usuario->idusuario);

    // Obtener el usuario autenticado y su rol
    $user = Auth::user();
    // Si tienes la relación 'rol' en el modelo Usuario:
    $rol = $user->rol->nombre ?? 'Sin rol';

    // Redirigir al panel de administración con mensaje de éxito
    return redirect()->route('admin')
        ->with('success', "¡Bienvenido {$user->nombre}! Tu rol es: {$rol}");
}

    //cerrar sesión
     public function logout(Request $request)
    {
       Auth::logout(); // Cierra la sesión
       $request->session()->invalidate(); // Invalida la sesión actual
       $request->session()->regenerateToken(); // Regenera el token CSRF
 
    return redirect('/login'); // Redirige al login
   }
    
}