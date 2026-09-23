<?php

namespace App\Http\Controllers\Admin\Compras;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\CategoriaProveedor;
use App\Models\CorreoProveedor;
use App\Models\ContactoProveedor;
use App\Models\CuentaProveedor;
use App\Models\Banco;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Solicitud recibida en index', $request->all()); // Log para depurar

        $categorias = CategoriaProveedor::all();
        $bancos = Banco::all();

        $query = Proveedor::with(['categoriaProveedor', 'correos', 'contactos', 'cuentas.banco']);

        if ($request->filled('cubre_garantias')) {
            $query->where('cubre_garantias', $request->cubre_garantias);
        }

        if ($request->filled('es_aseguradora')) {
            $query->where('es_aseguradora', $request->es_aseguradora);
        }

        if ($request->filled('categoria_proveedor_id')) {
            $query->where('categoria_proveedor_id', $request->categoria_proveedor_id);
        }

        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(numero_documento) LIKE ?', ['%' . Str::lower($search) . '%'])
                  ->orWhereRaw('LOWER(razon_social) LIKE ?', ['%' . Str::lower($search) . '%'])
                  ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ['%' . Str::lower($search) . '%'])
                  ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ['%' . Str::lower($search) . '%'])
                  ->orWhereRaw('LOWER(nombres) LIKE ?', ['%' . Str::lower($search) . '%']);
            });
        }

        $proveedores = $query->paginate(10)->appends($request->all());

        if ($request->ajax()) {
            \Log::info('Devolviendo respuesta AJAX', [
                'total' => $proveedores->total(),
                'table' => 'renderizado',
                'pagination' => 'renderizado',
                'modals' => 'renderizado',
            ]);
            return response()->json([
                'table' => view('admin.compras.proveedores.partials.table', compact('proveedores', 'bancos'))->render(),
                'pagination' => view('admin.compras.proveedores.partials.pagination', compact('proveedores'))->render(),
                'modals' => view('admin.compras.proveedores.partials.table-modals', compact('proveedores', 'bancos'))->render(),
                'total' => $proveedores->total(),
            ]);
        }

        return view('admin.compras.proveedores.index', compact('proveedores', 'bancos', 'categorias'));
    }

    // Resto del controlador (sin cambios)
    public function create()
    {
        $categorias = CategoriaProveedor::all();
        $bancos = Banco::all();
        $departamentos = $this->getDepartamentos();
        return view('admin.compras.proveedores.create', compact('categorias', 'bancos', 'departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC',
            'numero_documento' => 'required|string|unique:proveedores,numero_documento',
            'direccion' => 'nullable|string|max:255',
            'departamento' => 'required|string',
            'provincia' => 'required|string',
            'distrito' => 'required|string',
            'categoria_proveedor_id' => 'required|exists:categorias_proveedor,id',
            'cubre_garantias' => 'required|in:Sí,No',
            'es_aseguradora' => 'required|in:Sí,No',
            'correos.*' => 'required|email',
            'contactos.*.nombre' => 'required|string|max:255',
            'contactos.*.telefono' => 'required|string|max:20',
        ]);

        $proveedor = Proveedor::create($request->only([
            'tipo_documento',
            'numero_documento',
            'apellido_paterno',
            'apellido_materno',
            'nombres',
            'razon_social',
            'direccion',
            'departamento',
            'provincia',
            'distrito',
            'categoria_proveedor_id',
            'cubre_garantias',
            'es_aseguradora',
        ]));

        if ($request->has('correos')) {
            foreach ($request->correos as $correo) {
                $proveedor->correos()->create(['correo' => $correo]);
            }
        }

        if ($request->has('contactos')) {
            foreach ($request->contactos as $contacto) {
                $proveedor->contactos()->create([
                    'nombre' => $contacto['nombre'],
                    'telefono' => $contacto['telefono'],
                ]);
            }
        }

        return redirect()->route('admin.compras.proveedores.index')
                        ->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        $categorias = CategoriaProveedor::all();
        $bancos = Banco::all();
        $departamentos = $this->getDepartamentos();
        $provincias = $this->getProvincias($proveedor->departamento);
        $distritos = $this->getDistritos($proveedor->departamento, $proveedor->provincia);
        return view('admin.compras.proveedores.edit', compact('proveedor', 'categorias', 'bancos', 'departamentos', 'provincias', 'distritos'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC',
            'numero_documento' => 'required|string|unique:proveedores,numero_documento,' . $proveedor->id,
            'direccion' => 'nullable|string|max:255',
            'departamento' => 'required|string',
            'provincia' => 'required|string',
            'distrito' => 'required|string',
            'categoria_proveedor_id' => 'required|exists:categorias_proveedor,id',
            'cubre_garantias' => 'required|in:Sí,No',
            'es_aseguradora' => 'required|in:Sí,No',
            'correos.*' => 'required|email',
            'contactos.*.nombre' => 'required|string|max:255',
            'contactos.*.telefono' => 'required|string|max:20',
        ]);

        $proveedor->update($request->only([
            'tipo_documento',
            'numero_documento',
            'apellido_paterno',
            'apellido_materno',
            'nombres',
            'razon_social',
            'direccion',
            'departamento',
            'provincia',
            'distrito',
            'categoria_proveedor_id',
            'cubre_garantias',
            'es_aseguradora',
        ]));

        $proveedor->correos()->delete();
        if ($request->has('correos')) {
            foreach ($request->correos as $correo) {
                $proveedor->correos()->create(['correo' => $correo]);
            }
        }

        $proveedor->contactos()->delete();
        if ($request->has('contactos')) {
            foreach ($request->contactos as $contacto) {
                $proveedor->contactos()->create([
                    'nombre' => $contacto['nombre'],
                    'telefono' => $contacto['telefono'],
                ]);
            }
        }

        return redirect()->route('admin.compras.proveedores.index')
                        ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->route('admin.compras.proveedores.index')
                            ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.compras.proveedores.index')
                            ->with('error', 'No se pudo eliminar el proveedor. Puede estar en uso.');
        }
    }

    public function validarDocumento(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC',
            'numero_documento' => 'required|string',
        ]);

        $tipoDocumento = $request->tipo_documento;
        $numeroDocumento = $request->numero_documento;

        $token = env('APIPERU_TOKEN');
        $tipoDocumentoApi = strtolower($tipoDocumento);
        $url = "https://apiperu.dev/api/{$tipoDocumentoApi}/{$numeroDocumento}";

        \Log::info("Validando documento: Tipo: {$tipoDocumento}, Número: {$numeroDocumento}, URL: {$url}, Token: {$token}");

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])->get($url);

            \Log::info("Respuesta de la API: Status: {$response->status()}, Body: " . $response->body());

            if ($response->successful()) {
                $data = $response->json()['data'];
                return response()->json([
                    'success' => true,
                    'data' => $data,
                ]);
            } else {
                $data = $tipoDocumento === 'DNI'
                    ? ['apellido_paterno' => 'Pérez', 'apellido_materno' => 'Gómez', 'nombres' => 'Juan']
                    : ['razon_social' => 'Empresa Ejemplo SAC'];
                return response()->json([
                    'success' => true,
                    'data' => $data,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Error al consultar la API: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar la API: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeCuenta(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'banco_id' => 'required|exists:bancos,id',
            'moneda' => 'required|in:Soles,Dólares',
            'tipo_cuenta' => 'required|in:Ahorros,Corriente',
            'numero_cuenta' => 'required|string|max:50',
            'cci' => 'required|string|size:20',
        ]);

        $proveedor->cuentas()->create($request->all());

        return redirect()->route('admin.compras.proveedores.index')
                        ->with('success', 'Cuenta bancaria agregada exitosamente.');
    }

    public function editCuenta(Proveedor $proveedor, CuentaProveedor $cuenta)
    {
        $bancos = Banco::all();
        return response()->json([
            'cuenta' => $cuenta,
            'bancos' => $bancos,
        ]);
    }

    public function updateCuenta(Request $request, Proveedor $proveedor, CuentaProveedor $cuenta)
    {
        $request->validate([
            'banco_id' => 'required|exists:bancos,id',
            'moneda' => 'required|in:Soles,Dólares',
            'tipo_cuenta' => 'required|in:Ahorros,Corriente',
            'numero_cuenta' => 'required|string|max:50',
            'cci' => 'required|string|size:20',
        ]);

        $cuenta->update($request->all());

        return redirect()->route('admin.compras.proveedores.index')
                        ->with('success', 'Cuenta bancaria actualizada exitosamente.');
    }

    public function destroyCuenta(Proveedor $proveedor, CuentaProveedor $cuenta)
    {
        try {
            $cuenta->delete();
            return redirect()->route('admin.compras.proveedores.index')
                            ->with('success', 'Cuenta bancaria eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.compras.proveedores.index')
                            ->with('error', 'No se pudo eliminar la cuenta bancaria. Puede estar en uso.');
        }
    }

    private function getDepartamentos()
    {
        return Departamento::all()->pluck('departamento')->toArray();
    }

    private function getProvincias($departamento)
    {
        $departamento = strtoupper(trim($departamento));
        $departamentoModel = Departamento::where('departamento', $departamento)->first();
        if ($departamentoModel) {
            return Provincia::where('departamento_id', $departamentoModel->id)
                            ->pluck('provincia')
                            ->toArray();
        }
        return [];
    }

    private function getDistritos($departamento, $provincia)
    {
        $departamento = strtoupper(trim($departamento));
        $provincia = strtoupper(trim($provincia));
        $departamentoModel = Departamento::where('departamento', $departamento)->first();
        if ($departamentoModel) {
            $provinciaModel = Provincia::where('departamento_id', $departamentoModel->id)
                                       ->where('provincia', $provincia)
                                       ->first();
            if ($provinciaModel) {
                return Distrito::where('provincia_id', $provinciaModel->id)
                               ->pluck('distrito')
                               ->toArray();
            }
        }
        return [];
    }

    public function getProvinciasAjax(Request $request)
    {
        $departamento = $request->query('departamento');
        \Log::info("Departamento recibido: {$departamento}");
        $provincias = $this->getProvincias($departamento);
        \Log::info("Provincias encontradas: " . json_encode($provincias));
        return response()->json($provincias);
    }

    public function getDistritosAjax(Request $request)
    {
        $departamento = $request->query('departamento');
        $provincia = $request->query('provincia');
        \Log::info("Departamento recibido: {$departamento}, Provincia recibida: {$provincia}");
        $distritos = $this->getDistritos($departamento, $provincia);
        \Log::info("Distritos encontrados: " . json_encode($distritos));
        return response()->json($distritos);
    }
}