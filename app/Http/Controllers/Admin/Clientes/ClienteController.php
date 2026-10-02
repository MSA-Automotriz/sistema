<?php

namespace App\Http\Controllers\Admin\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\CategoriaCliente;
use App\Models\CanalCaptacion;
use App\Models\Telefono;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\DocumentValidationService;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // Obtener los parámetros de filtrado
        $tipo_cliente = $request->tipo_cliente;
        $categoria_cliente_id = $request->categoria_cliente_id;
        $canal_captacion_id = $request->canal_captacion_id;
        $query = $request->query('query');
        
        // Construir la consulta base
        $clientesQuery = Cliente::with('categoria', 'canalCaptacion', 'telefonos');
        
        // Aplicar filtros si están presentes
        if ($tipo_cliente) {
            $clientesQuery->where('tipo_cliente', $tipo_cliente);
        }
        
        if ($categoria_cliente_id) {
            $clientesQuery->where('categoria_cliente_id', $categoria_cliente_id);
        }
        
        if ($canal_captacion_id) {
            $clientesQuery->where('canal_captacion_id', $canal_captacion_id);
        }
        
        if ($query) {
            $clientesQuery->where(function($q) use ($query) {
                $q->where('documento_identidad', 'LIKE', "%{$query}%")
                ->orWhere('nombres', 'LIKE', "%{$query}%")
                ->orWhere('apellido_paterno', 'LIKE', "%{$query}%")
                ->orWhere('apellido_materno', 'LIKE', "%{$query}%")
                ->orWhere('razon_social', 'LIKE', "%{$query}%");
            });
        }
        
        // Obtener los resultados paginados
        $clientes = $clientesQuery->paginate(10)->withQueryString();
        
        // Si es una solicitud AJAX, devolver solo la tabla de resultados
        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.clientes.partials.table', compact('clientes'))->render(),
                'pagination' => view('admin.clientes.partials.pagination', compact('clientes'))->render(),
                'totalClientes' => $clientesQuery->count(),
            ]);
        }
        
        // Para la carga inicial de la página
        $totalClientes = Cliente::count();
        $categorias = CategoriaCliente::all();
        $canales = CanalCaptacion::all();
        
        return view('admin.clientes.index', compact('clientes', 'totalClientes', 'categorias', 'canales'));
    }

    public function create()
    {
        $categorias = CategoriaCliente::all();
        $canales = CanalCaptacion::all();
        
        // Obtener lista de departamentos para el formulario
        $departamentos = $this->getDepartamentos();
            
        return view('admin.clientes.create', compact('categorias', 'canales', 'departamentos'));
    }

    public function store(Request $request)
{
    $request->validate([
        'documento_identidad' => 'required|unique:clientes',
        'tipo_cliente' => 'required|in:natural,juridica',
        'apellido_paterno' => 'required_if:tipo_cliente,natural|nullable|string|max:255',
        'apellido_materno' => 'required_if:tipo_cliente,natural|nullable|string|max:255',
        'nombres' => 'required_if:tipo_cliente,natural|nullable|string|max:255',
        'razon_social' => 'required_if:tipo_cliente,juridica|nullable|string|max:255',
        'departamento' => 'required|string|max:255',
        'provincia' => 'required|string|max:255',
        'distrito' => 'required|string|max:255',
        'correo' => 'nullable|email|max:255',
        'categoria_cliente_id' => 'required|exists:categoria_clientes,id',
        'canal_captacion_id' => 'required|exists:canal_captacion,id',
        'telefonos.*' => 'nullable|string|max:20',
        'celulares.*' => 'nullable|string|max:20',
    ]);

    // Preparar datos según el tipo de cliente
    $clienteData = $request->only([
        'documento_identidad', 'tipo_cliente', 'departamento', 
        'provincia', 'distrito', 'correo', 'categoria_cliente_id', 'canal_captacion_id'
    ]);

    if ($request->tipo_cliente === 'natural') {
        $clienteData['apellido_paterno'] = $request->apellido_paterno;
        $clienteData['apellido_materno'] = $request->apellido_materno;
        $clienteData['nombres'] = $request->nombres;
        $clienteData['razon_social'] = null;
    } else { // juridica
        $clienteData['apellido_paterno'] = null;
        $clienteData['apellido_materno'] = null;
        $clienteData['nombres'] = null;
        $clienteData['razon_social'] = $request->razon_social;
    }

    $cliente = Cliente::create($clienteData);

    // Guardar teléfonos
    if ($request->telefonos) {
        foreach ($request->telefonos as $numero) {
            if ($numero) {
                $cliente->telefonos()->create(['numero' => $numero, 'tipo' => 'telefono']);
            }
        }
    }

    // Guardar celulares
    if ($request->celulares) {
        foreach ($request->celulares as $numero) {
            if ($numero) {
                $cliente->telefonos()->create(['numero' => $numero, 'tipo' => 'celular']);
            }
        }
    }

    return redirect()->route('admin.clientes.index')->with('success', 'Cliente creado con éxito');
}

public function buscar(Request $request)
{
    $query = $request->input('query');
    
    if (empty($query)) {
        return response()->json([]);
    }
    
    // Buscar clientes por DNI, RUC o nombre
    $clientes = Cliente::where('documento_identidad', 'like', "%{$query}%")
        ->orWhere('nombres', 'like', "%{$query}%")
        ->orWhere('apellido_paterno', 'like', "%{$query}%")
        ->orWhere('apellido_materno', 'like', "%{$query}%")
        ->orWhere('razon_social', 'like', "%{$query}%")
        ->limit(10)
        ->get();
    
    // Devolver resultados como JSON (sin paginar)
    return response()->json($clientes);
}

    public function show(Cliente $cliente)
    {
        $cliente->load('categoria', 'canalCaptacion', 'telefonos');
        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $categorias = CategoriaCliente::all();
        $canales = CanalCaptacion::all();
        $cliente->load('telefonos');
        
        // Obtener lista de departamentos para el formulario
        $departamentos = $this->getDepartamentos();
        $provincias = $this->getProvincias($cliente->departamento);
        $distritos = $this->getDistritos($cliente->departamento, $cliente->provincia);
            
        return view('admin.clientes.edit', compact('cliente', 'categorias', 'canales', 'departamentos', 'provincias', 'distritos'));
    }

    public function update(Request $request, Cliente $cliente)
{
    $request->validate([
        'documento_identidad' => 'required|unique:clientes,documento_identidad,' . $cliente->id,
        'tipo_cliente' => 'required|in:natural,juridica',
        'apellido_paterno' => 'required_if:tipo_cliente,natural|nullable|string|max:255',
        'apellido_materno' => 'required_if:tipo_cliente,natural|nullable|string|max:255',
        'nombres' => 'required_if:tipo_cliente,natural|nullable|string|max:255',
        'razon_social' => 'required_if:tipo_cliente,juridica|nullable|string|max:255',
        'departamento' => 'required|string|max:255',
        'provincia' => 'required|string|max:255',
        'distrito' => 'required|string|max:255',
        'correo' => 'nullable|email|max:255',
        'categoria_cliente_id' => 'required|exists:categoria_clientes,id',
        'canal_captacion_id' => 'required|exists:canal_captacion,id',
        'telefonos.*' => 'nullable|string|max:20',
        'celulares.*' => 'nullable|string|max:20',
    ]);

    // Preparar datos según el tipo de cliente
    $clienteData = $request->only([
        'documento_identidad', 'tipo_cliente', 'departamento', 
        'provincia', 'distrito', 'correo', 'categoria_cliente_id', 'canal_captacion_id'
    ]);

    if ($request->tipo_cliente === 'natural') {
        $clienteData['apellido_paterno'] = $request->apellido_paterno;
        $clienteData['apellido_materno'] = $request->apellido_materno;
        $clienteData['nombres'] = $request->nombres;
        $clienteData['razon_social'] = null;
    } else { // juridica
        $clienteData['apellido_paterno'] = null;
        $clienteData['apellido_materno'] = null;
        $clienteData['nombres'] = null;
        $clienteData['razon_social'] = $request->razon_social;
    }

    $cliente->update($clienteData);

    // Actualizar teléfonos y celulares
    $cliente->telefonos()->delete();
    if ($request->telefonos) {
        foreach ($request->telefonos as $numero) {
            if ($numero) {
                $cliente->telefonos()->create(['numero' => $numero, 'tipo' => 'telefono']);
            }
        }
    }
    if ($request->celulares) {
        foreach ($request->celulares as $numero) {
            if ($numero) {
                $cliente->telefonos()->create(['numero' => $numero, 'tipo' => 'celular']);
            }
        }
    }

    return redirect()->route('admin.clientes.index')->with('success', 'Cliente actualizado con éxito');
}

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('admin.clientes.index')->with('success', 'Cliente eliminado con éxito');
    }
    
    /**
     * Valida un documento (DNI o RUC) con API Perú
     */
    public function validarDocumento(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC',
            'numero_documento' => 'required|string'
        ]);

        $tipoDocumento = $request->tipo_documento;
        $numeroDocumento = trim($request->numero_documento);

        Log::info("Validando documento: Tipo: {$tipoDocumento}, Número: {$numeroDocumento}");

        try {
            $validationService = new DocumentValidationService();
            
            if ($tipoDocumento === 'DNI') {
                $result = $validationService->validateDni($numeroDocumento);
            } else {
                $result = $validationService->validateRuc($numeroDocumento);
            }
            
            if ($result['success']) {
                // Si es RUC y no tiene ubicación completa, intentar extraerla de la dirección
                if ($tipoDocumento === 'RUC' && isset($result['data']['direccion'])) {
                    $ubigeo = $this->extraerUbigeoDeDireccion($result['data']['direccion']);
                    if (!empty($ubigeo['departamento']) && empty($result['data']['departamento'])) {
                        $result['data']['departamento'] = $ubigeo['departamento'];
                        $result['data']['provincia'] = $ubigeo['provincia'];
                        $result['data']['distrito'] = $ubigeo['distrito'];
                    }
                }
                
                Log::info("Documento validado exitosamente", [
                    'documento' => $numeroDocumento,
                    'source' => $result['source'] ?? 'unknown'
                ]);
            } else {
                Log::warning("No se pudo validar el documento: {$numeroDocumento}");
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error("Error inesperado al validar documento: " . $e->getMessage(), [
                'documento' => $numeroDocumento,
                'tipo' => $tipoDocumento
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Por favor, intente nuevamente.'
            ], 500);
        }
    }
    
    /**
     * Extrae información de ubicación de una dirección
     */
    private function extraerUbigeoDeDireccion($direccion)
    {
        // Esta es una implementación simplificada
        $resultado = [
            'departamento' => '',
            'provincia' => '',
            'distrito' => ''
        ];
        
        // Intentar extraer el departamento
        $departamentos = DB::table('departamentos')->pluck('departamento')->toArray();
        foreach ($departamentos as $departamento) {
            if (stripos($direccion, $departamento) !== false) {
                $resultado['departamento'] = $departamento;
                break;
            }
        }
        
        // Si encontramos departamento, buscar provincia
        if ($resultado['departamento']) {
            $provincias = DB::table('provincias')
                ->join('departamentos', 'provincias.departamento_id', '=', 'departamentos.id')
                ->where('departamentos.departamento', $resultado['departamento'])
                ->pluck('provincias.provincia')
                ->toArray();
                
            foreach ($provincias as $provincia) {
                if (stripos($direccion, $provincia) !== false) {
                    $resultado['provincia'] = $provincia;
                    break;
                }
            }
        }
        
        // Si encontramos provincia, buscar distrito
        if ($resultado['provincia']) {
            $distritos = DB::table('distritos')
                ->join('provincias', 'distritos.provincia_id', '=', 'provincias.id')
                ->join('departamentos', 'provincias.departamento_id', '=', 'departamentos.id')
                ->where('departamentos.departamento', $resultado['departamento'])
                ->where('provincias.provincia', $resultado['provincia'])
                ->pluck('distritos.distrito')
                ->toArray();
                
            foreach ($distritos as $distrito) {
                if (stripos($direccion, $distrito) !== false) {
                    $resultado['distrito'] = $distrito;
                    break;
                }
            }
        }
        
        return $resultado;
    }
    
    /**
     * Obtiene la lista de departamentos
     */
    private function getDepartamentos()
    {
        return Departamento::all()->pluck('departamento')->toArray();
    }

    /**
     * Obtiene las provincias de un departamento
     */
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

    /**
     * Obtiene los distritos de una provincia
     */
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

    /**
     * Endpoint para obtener provincias vía AJAX
     */
    public function getProvinciasAjax(Request $request)
    {
        $departamento = $request->query('departamento');
        Log::info("Departamento recibido: {$departamento}");
        $provincias = $this->getProvincias($departamento);
        Log::info("Provincias encontradas: " . json_encode($provincias));
        return response()->json($provincias);
    }

    /**
     * Endpoint para obtener distritos vía AJAX
     */
    public function getDistritosAjax(Request $request)
    {
        $departamento = $request->query('departamento');
        $provincia = $request->query('provincia');
        Log::info("Departamento recibido: {$departamento}, Provincia recibida: {$provincia}");
        $distritos = $this->getDistritos($departamento, $provincia);
        Log::info("Distritos encontrados: " . json_encode($distritos));
        return response()->json($distritos);
    }

    /**
     * Buscar cliente para el módulo de mantenimiento
     */
    public function buscarParaMantenimiento(Request $request)
    {
        $query = $request->get('query');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        // Búsqueda simple sin paginación
        $clientes = Cliente::where('documento_identidad', 'like', '%' . $query . '%')
            ->orWhere('razon_social', 'like', '%' . $query . '%')
            ->orWhere(DB::raw("CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno)"), 'like', '%' . $query . '%')
            ->with('telefonos')
            ->limit(10)
            ->get();
            
        return response()->json($clientes);
    }

/**
 * Guardar cliente desde el módulo de mantenimiento
 */
/**
 * Guardar cliente desde el módulo de mantenimiento
 */
public function guardarParaMantenimiento(Request $request)
{
    \Log::info('==== INICIO PROCESO DE GUARDAR CLIENTE ====');
    \Log::info('Datos recibidos:', $request->all());
    
    try {
        // Validación de datos
        \Log::info('Iniciando validación de datos');
        
        $rules = [
            'documento_identidad' => 'required|string|max:20|unique:clientes',
            'tipo_cliente' => 'required|in:persona,empresa,natural,juridica',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'nombres' => 'nullable|string|max:100',
            'razon_social' => 'nullable|string|max:200',
            'departamento' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'correo' => 'nullable|email|max:100',
        ];
        
        \Log::info('Reglas de validación:', $rules);
        
        $validator = \Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            \Log::warning('Validación fallida. Errores:', $errors);
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $errors
            ], 422);
        }
        
        \Log::info('Validación exitosa');
        
        DB::beginTransaction();
        \Log::info('Iniciando transacción DB');
        
        // Normalizar el tipo de cliente
        $tipoClienteOriginal = $request->tipo_cliente;
        $tipoClienteNormalizado = $tipoClienteOriginal;
        
        // Convertir valores si es necesario para la base de datos
        if ($tipoClienteOriginal === 'persona') {
            $tipoClienteNormalizado = 'natural';
        } else if ($tipoClienteOriginal === 'empresa') {
            $tipoClienteNormalizado = 'juridica';
        }
            
        \Log::info("Tipo cliente original: $tipoClienteOriginal, normalizado: $tipoClienteNormalizado");
        
        // Preparar datos del cliente
        $clienteData = [
            'documento_identidad' => $request->documento_identidad,
            'tipo_cliente' => $tipoClienteNormalizado,
            'departamento' => $request->departamento ?? '',
            'provincia' => $request->provincia ?? '',
            'distrito' => $request->distrito ?? '',
            'correo' => $request->correo,
            'direccion' => $request->direccion ?? '',
            'ocupacion' => $request->ocupacion ?? '',
            'categoria_cliente_id' => $request->categoria_cliente_id ?? 1,
            'canal_captacion_id' => $request->canal_captacion_id ?? 1,
        ];
        
        // Establecer campos según tipo de cliente
        if ($tipoClienteNormalizado === 'natural') {
            $clienteData['apellido_paterno'] = $request->apellido_paterno ?? '';
            $clienteData['apellido_materno'] = $request->apellido_materno ?? '';
            $clienteData['nombres'] = $request->nombres ?? '';
            $clienteData['razon_social'] = null;
            \Log::info("Estableciendo campos para cliente tipo persona/natural");
        } else { // juridica/empresa
            $clienteData['apellido_paterno'] = null;
            $clienteData['apellido_materno'] = null;
            $clienteData['nombres'] = null;
            $clienteData['razon_social'] = $request->razon_social ?? '';
            \Log::info("Estableciendo campos para cliente tipo empresa/juridica");
        }
        
        \Log::info('Datos finales a guardar:', $clienteData);
        
        // Crear el cliente
        \Log::info('Creando registro de cliente...');
        $cliente = Cliente::create($clienteData);
        \Log::info('Cliente creado con ID: ' . $cliente->id);
        
        // Guardar teléfonos
        \Log::info('Procesando teléfonos...');
        
        if ($request->has('telefonos')) {
            \Log::info('Teléfonos encontrados en la solicitud. Tipo: ' . gettype($request->telefonos));
            
            if (is_array($request->telefonos)) {
                foreach ($request->telefonos as $key => $valor) {
                    \Log::info("Procesando teléfono[$key]: " . (is_array($valor) ? json_encode($valor) : $valor));
                    
                    $numero = '';
                    $tipo = 'celular';
                    
                    // Determinar el formato de los teléfonos
                    if (is_array($valor) && isset($valor['numero'])) {
                        $numero = $valor['numero'];
                        $tipo = $valor['tipo'] ?? 'celular';
                        \Log::info("Teléfono en formato array. Número: $numero, Tipo: $tipo");
                    } elseif (is_string($valor) && !empty($valor)) {
                        $numero = $valor;
                        \Log::info("Teléfono en formato string. Número: $numero");
                    } elseif (is_string($key) && is_string($valor) && !empty($valor)) {
                        $numero = $valor;
                        $tipo = $key === 'telefonos' ? 'fijo' : 'celular';
                        \Log::info("Teléfono en formato key-value. Número: $numero, Tipo: $tipo");
                    }
                    
                    if (!empty($numero)) {
                        \Log::info("Guardando teléfono: $numero ($tipo)");
                        Telefono::create([
                            'cliente_id' => $cliente->id,
                            'numero' => $numero,
                            'tipo' => $tipo,
                        ]);
                    } else {
                        \Log::info("Teléfono vacío, no se guarda");
                    }
                }
            } elseif (is_string($request->telefonos) && !empty($request->telefonos)) {
                \Log::info("Teléfono único como string: {$request->telefonos}");
                Telefono::create([
                    'cliente_id' => $cliente->id,
                    'numero' => $request->telefonos,
                    'tipo' => 'celular',
                ]);
            } else {
                \Log::info("Formato de teléfonos no reconocido");
            }
        } else {
            \Log::info("No se encontraron teléfonos en la solicitud");
        }
        
        // Si hay celulares separados
        if ($request->has('celulares')) {
            \Log::info('Celulares encontrados en la solicitud. Tipo: ' . gettype($request->celulares));
            
            if (is_array($request->celulares)) {
                foreach ($request->celulares as $key => $celular) {
                    \Log::info("Procesando celular[$key]: " . (is_string($celular) ? $celular : json_encode($celular)));
                    
                    if (!empty($celular)) {
                        \Log::info("Guardando celular: $celular");
                        Telefono::create([
                            'cliente_id' => $cliente->id,
                            'numero' => $celular,
                            'tipo' => 'celular',
                        ]);
                    }
                }
            } elseif (is_string($request->celulares) && !empty($request->celulares)) {
                \Log::info("Celular único como string: {$request->celulares}");
                Telefono::create([
                    'cliente_id' => $cliente->id,
                    'numero' => $request->celulares,
                    'tipo' => 'celular',
                ]);
            }
        }
        
        \Log::info('Confirmando transacción...');
        DB::commit();
        \Log::info('Transacción confirmada exitosamente');
        
        $clienteConTelefonos = $cliente->fresh(['telefonos']);
        \Log::info('Cliente cargado con teléfonos:', [
            'id' => $clienteConTelefonos->id,
            'telefonos' => $clienteConTelefonos->telefonos->toArray()
        ]);
        
        \Log::info('==== FIN PROCESO DE GUARDAR CLIENTE: ÉXITO ====');
        
        return response()->json([
            'success' => true,
            'cliente' => $clienteConTelefonos,
            'message' => 'Cliente creado correctamente'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('==== ERROR AL GUARDAR CLIENTE ====');
        \Log::error('Mensaje: ' . $e->getMessage());
        \Log::error('Archivo: ' . $e->getFile() . ' (Línea: ' . $e->getLine() . ')');
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al crear el cliente: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
}

/**
 * Normaliza el tipo de cliente a 'persona' o 'empresa'
 */
private function normalizarTipoCliente($tipoCliente)
{
    // Convertir a minúsculas y quitar espacios
    $tipoCliente = strtolower(trim($tipoCliente));
    
    // Mapeo de tipos de cliente
    $mapeo = [
        'natural' => 'persona',
        'juridica' => 'empresa',
        'persona natural' => 'persona',
        'persona juridica' => 'empresa',
        'persona' => 'persona',
        'empresa' => 'empresa'
    ];
    
    // Devolver el tipo normalizado o por defecto 'persona'
    return $mapeo[$tipoCliente] ?? 'persona';
}
public function debugRequest(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'documento_identidad' => 'required|string|max:20|unique:clientes',
        'tipo_cliente' => 'required|in:persona,empresa,natural,juridica',
        'apellido_paterno' => 'nullable|string|max:100',
        'apellido_materno' => 'nullable|string|max:100',
        'nombres' => 'nullable|string|max:100',
        'razon_social' => 'nullable|string|max:200',
        'departamento' => 'nullable|string|max:100',
        'provincia' => 'nullable|string|max:100',
        'distrito' => 'nullable|string|max:100',
        'direccion' => 'nullable|string|max:200',
        'correo' => 'nullable|email|max:100',
    ]);
    
    return response()->json([
        'datos' => $request->all(),
        'errores' => $validator->fails() ? $validator->errors()->toArray() : [],
        'tipo_cliente_recibido' => $request->tipo_cliente,
        'formato_tipo_cliente' => gettype($request->tipo_cliente)
    ]);
}
}