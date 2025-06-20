<?php
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
        $this->middleware(Perm::class.':salidas.index' )->only('index');
        $this->middleware(Perm::class.':salidas.create')->only(['create','store']);
    }

    /* ───── LISTA ───── */
    public function index()
    {
        $salidas = SalidaInsumo::with('usuario')
                     ->orderByDesc('fecha')
                     ->paginate(10);

        return view('salidas.index', compact('salidas'));
    }

    /* ═════ PLANTILLA (50 hamb = 20 S + 20 D + ❗ 3 T) ═════ */
    private function base(): array
    {
        return [
            10 => 50,   // Pan
            11 => 1,    // Queso
            9  => 8,    // Tomate
            12 => 3,    // Lechuga
            13 => 95,   // Carne
            14 => 8,    // Pepinillos
            15 => 14,   // Papa
            16 => 3,    // Aceite
        ];
    }
    private function plantilla(int $factor = 1): array
    {
        return collect($this->base())
               ->map(fn($c) => $c * $factor)
               ->all();
    }

    /* ───── CREATE ───── */
    public function create()
    {
        return view('salidas.create', [
            'insumos'      => Insumo::orderBy('nombre')->get(),
            'necesarioDia' => $this->plantilla(1),   // 50 hamburguesas
            'necesarioFin' => $this->plantilla(2),   // 100 hamburguesas
        ]);
    }

    /* ───── STORE ───── */
     public function store(Request $r)
{
    /* 0. ¿ya existe salida para ese día? */
    if (SalidaInsumo::whereDate('fecha', $r->fecha)->exists()) {
        return back()->with('error','Ya existe una salida para esa fecha.')->withInput();
    }

    /* 1. Validación */
    $r->validate([
        'fecha'               => 'required|date',
        'detalles'            => 'required|array|size:8',
        'detalles.*.idinsumo' => 'required|exists:insumo,idinsumo|distinct',
        'detalles.*.cantidad' => 'required|numeric|min:0.01',
    ],[
        'detalles.size' => 'Debes registrar exactamente los 8 insumos.',
    ]);

    /* 2. Verificación de mínimos (igual que antes) */
    $det        = $r->detalles;
    $minimos    = collect($this->base());
    foreach ($det as $d){
        if ($d['cantidad'] < $minimos[$d['idinsumo']]){
            $nom = Insumo::find($d['idinsumo'])->nombre;
            return back()->with('error',"Cantidad de «$nom» insuficiente (mínimo {$minimos[$d['idinsumo']]}).")
                         ->withInput();
        }
    }

    /* 3. Transacción: inserta Salida + Detalles */
    DB::beginTransaction();
    try{
        $salida = SalidaInsumo::create([
            'fecha'       => $r->fecha,          // solo DATE
            'observacion' => $r->observacion,
            'idusuario'   => Auth::id(),
        ]);

        foreach ($det as $d){
            DetalleSalidaInsumo::create([
                'idsalida' => $salida->idsalida,
                'idinsumo' => $d['idinsumo'],
                'cantidad' => $d['cantidad'],
            ]);
        }

        DB::commit();          // ✅ confirma primero
    }catch(\Throwable $e){
        DB::rollBack();
        return back()->with('error','Error: '.$e->getMessage())->withInput();
    }

    /* 4. Ahora sí llama a la SP (ya ve la salida confirmada) */
    DB::statement('CALL generar_stock_menu(?)', [$r->fecha]);

    return redirect()
           ->route('salidas.index')
           ->with('success','Salida registrada y stock de menú actualizado.');
}

}
