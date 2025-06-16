<?php 
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Validation\Rule;
use App\Http\Middleware\CheckPermission as Perm;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class . ':products.index')->only('index');
        $this->middleware(Perm::class . ':products.create')->only(['create','store']);
        $this->middleware(Perm::class . ':products.edit')->only(['edit','update']);
        $this->middleware(Perm::class . ':products.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Product::with('categoria')
                    ->where('estado', 1);
    
        // Filtro por búsqueda (nombre o categoría)
        if ($request->filled('q')) {
            $q = trim($request->q);
    
            $query->where(function($sub) use ($q) {
                $sub->where('nombre', 'like', "%$q%")
                    ->orWhereHas('categoria', function($cat) use ($q) {
                        $cat->where('nombre', 'like', "%$q%");
                    });
            });
        }
    
        $products = $query->orderBy('nombre')->paginate(10);
    
        return view('products.index', compact('products'));
    }
    
    

    public function create()
    {
        return view('products.create', [
            'product' => new Product(),
            'categorias' => Categoria::where('estado',1)->orderBy('nombre')->get()
        ]);
    }

    public function store(Request $r)
{
    try {
        // Validaciones base
        $data = $r->validate([
            'nombre'          => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u|min:3|max:25',
            'idcategoria'     => 'required|exists:categoria,idcategoria',
            'precio_unitario' => 'required|integer|min:1',
            'descripcion'     => 'nullable|max:255',
            'imagenUrl'       => 'nullable|url|max:250',
        ]);

        // Normalizar el nombre
        $data['nombre'] = trim(preg_replace('/\s+/', ' ', $data['nombre']));

        // Verificar si ya existe un producto igual
        $existe = Product::whereRaw('LOWER(nombre) = ?', [strtolower($data['nombre'])])
            ->where('idcategoria', $data['idcategoria'])
            ->where('precio_unitario', $data['precio_unitario'])
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Ya existe una hamburguesa con ese mismo nombre, categoría y precio.');
        }

        // Guardar el producto
        Product::create($data + ['estado' => 1]);

        return redirect()->route('products.index')
                         ->with('success', 'Hamburguesa registrada correctamente.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error al registrar hamburguesa: ' . $e->getMessage());
    }
}


      
    

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product'    => $product,
            'categorias' => Categoria::where('estado',1)->orderBy('nombre')->get()
        ]);
    }

    public function update(Request $r, Product $product)
{
    try {
        $data = $r->validate([
            'nombre'          => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u|min:3|max:25',
            'idcategoria'     => 'required|exists:categoria,idcategoria',
            'precio_unitario' => 'required|numeric|min:1',
            'descripcion'     => 'nullable|max:255',
            'imagenUrl'       => 'nullable|url|max:250',
        ]);

        // Normalizar nombre
        $data['nombre'] = trim(preg_replace('/\s+/', ' ', $data['nombre']));

        // Verificar duplicado
         $esDuplicado = Product::where('idhamburguesa', '!=', $product->idhamburguesa)
    ->whereRaw('LOWER(nombre) = ?', [strtolower($data['nombre'])])
    ->where('idcategoria', $data['idcategoria'])
    ->where('precio_unitario', $data['precio_unitario'])
    ->first();

if ($esDuplicado) {
    return back()->withInput()->with('error', 'Ya existe una hamburguesa con ese mismo nombre, categoría y precio.');
}


        // Llenar datos
        $product->fill($data);

        // Verificar si no hubo ningún cambio
        if (!$product->isDirty()) {
            return back()->with('info', 'No se realizó ningún cambio.');
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Hamburguesa actualizada correctamente.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error al actualizar hamburguesa: ' . $e->getMessage());
    }
}


 
    
    

    public function destroy(Product $product)
    {
        $product->update(['estado' => 0]); // baja lógica
        return back()->with('success', 'Producto desactivado.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
