<?php
// app/Http/Controllers/SalidaController.php
namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\{SalidaInsumo, DetalleSalidaInsumo, Insumo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class.':salidas.index')->only('index');
        $this->middleware(Perm::class.':salidas.create')->only(['create','store']);
    }

    public function index()
    {
        $salidas = SalidaInsumo::with('usuario')
                    ->orderByDesc('fecha')
                    ->paginate(10);

        return view('salidas.index', compact('salidas'));
    }

    public function create()
    {
        return view('salidas.create', [
            'insumos' => Insumo::orderBy('nombre')->get()
        ]);
    }

    public function store(Request $request)
    {
        $det = $request->input('detalles', []);

        if (!$det) return back()->with('error','Debe agregar al menos un insumo.')->withInput();

        DB::beginTransaction();
        try {
            $salida = SalidaInsumo::create([
                'fecha'       => $request->fecha ?? now()->toDateString(),
                'idusuario'   => Auth::id(),
                'observacion' => $request->observacion,
            ]);

            foreach ($det as $d) {
                DetalleSalidaInsumo::create([
                    'idsalida' => $salida->idsalida,
                    'idinsumo' => $d['idinsumo'],
                    'cantidad' => $d['cantidad'],
                ]);
            }

            DB::commit();
            return redirect()->route('salidas.index')->with('success','Salida registrada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Error: '.$e->getMessage())->withInput();
        }
    }
}
