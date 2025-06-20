<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\{Product, Insumo, Receta};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    /* ───── INSUMOS CRÍTICOS (solo carne y queso) ───── */
    private const CARNE_ID = 13;   // ← pon aquí el ID real de “Carne molida”
    private const QUESO_ID = 11;   // ← pon aquí el ID real de “Queso”
    private const INSUMOS_CRITICOS = [self::CARNE_ID, self::QUESO_ID];

    /* ───── MAPA cantidad esperada por tipo ─────
       hamburguesaID => [insumoID => cantidad]      */
    private const REGLA_CANTIDADES = [
        1 => [self::CARNE_ID=>1, self::QUESO_ID=>1],  // Simple
        2 => [self::CARNE_ID=>2, self::QUESO_ID=>2],  // Doble
        3 => [self::CARNE_ID=>3, self::QUESO_ID=>3],  // Triple
    ];

    public function __construct()
    {
        $this->middleware(Perm::class.':recetas.index')->only('index');
        $this->middleware(Perm::class.':recetas.create')->only(['create','store']);
        $this->middleware(Perm::class.':recetas.edit')->only(['edit','update']);
        $this->middleware(Perm::class.':recetas.delete')->only('destroy');
    }

    /* ---------- LISTAR ---------- */
    public function index()
    {
        $recetas = Receta::with(['hamburguesa','insumo'])
                         ->orderBy('idhamburguesa')
                         ->orderBy('idreceta')
                         ->get();

        return view('recetas.index', compact('recetas'));
    }

    /* ---------- FORM CREAR ---------- */
    public function create()
    {
        return view('recetas.create', [
            'hamburguesas' => Product::orderBy('nombre')->get(),
            'insumos'      => Insumo::whereIn('idinsumo', self::INSUMOS_CRITICOS)
                                    ->orderBy('nombre')->get(),
        ]);
    }

    /* ---------- GUARDAR ---------- */
    public function store(Request $r)
    {
        $this->validarLinea($r);

        Receta::create($r->only('idhamburguesa','idinsumo','cantidad_necesaria'));
        DB::statement('CALL generar_stock_menu(?)', [today()]);

        return redirect()->route('recetas.index')
                         ->with('success','Receta registrada.');
    }

    /* ---------- FORM EDITAR ---------- */
    public function edit(Receta $receta)
    {
        return view('recetas.edit', [
            'receta'       => $receta,
            'hamburguesas' => Product::orderBy('nombre')->get(),
            'insumos'      => Insumo::whereIn('idinsumo', self::INSUMOS_CRITICOS)
                                    ->orderBy('nombre')->get(),
        ]);
    }

    /* ---------- ACTUALIZAR ---------- */
    public function update(Request $r, Receta $receta)
    {
        $this->validarLinea($r, $receta->idreceta);

        $receta->update($r->only('cantidad_necesaria'));
        DB::statement('CALL generar_stock_menu(?)', [today()]);

        return redirect()->route('recetas.index')
                         ->with('success','Receta actualizada.');
    }

    /* ---------- ELIMINAR ---------- */
    public function destroy(Receta $receta)
    {
        $usada = DB::table('detalle_venta')
                   ->where('idhamburguesa', $receta->idhamburguesa)
                   ->exists();
        if ($usada) {
            return back()->with('error','No se puede borrar: ya existen ventas.');
        }

        $receta->delete();
        DB::statement('CALL generar_stock_menu(?)', [today()]);

        return back()->with('success','Receta eliminada.');
    }

    /* ---------- VALIDACIÓN CENTRAL ---------- */
    private function validarLinea(Request $r, ?int $idReceta = null): void
    {
        $r->validate([
            'idhamburguesa'      => 'required|exists:hamburguesa,idhamburguesa',
            'idinsumo'           => [
                'required',
                Rule::in(self::INSUMOS_CRITICOS),                 // solo carne y queso
                Rule::unique('receta')
                    ->ignore($idReceta, 'idreceta')
                    ->where('idhamburguesa', $r->idhamburguesa),
            ],
            'cantidad_necesaria' => 'required|numeric|min:0.01|max:9999.99',
        ]);

        /*  Regla de cantidades esperadas  */
        $burger = (int) $r->idhamburguesa;
        $ins    = (int) $r->idinsumo;

        if (isset(self::REGLA_CANTIDADES[$burger][$ins])) {
            $esperada = self::REGLA_CANTIDADES[$burger][$ins];
            if ((float) $r->cantidad_necesaria !== (float) $esperada) {
                abort(
                    back()
                      ->withInput()
                      ->withErrors([
                        'cantidad_necesaria' => "Para esa hamburguesa la cantidad debe ser $esperada."
                      ])
                      ->getSession()
                      ->get('_redirect')
                );
            }
        }
    }
}
