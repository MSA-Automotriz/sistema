<?php
namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoMantenimiento;
use App\Models\VehiculoMantenimiento;
use App\Models\DetalleCotizacion;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class CotizacionOrdenTrabajoController extends Controller
{
   public function index(Cotizacion $cotizacion)
   {
       Log::info('Iniciando índice de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id
       ]);

       // Obtener la orden de trabajo existente
       $orden = $cotizacion->ordenTrabajo;
       
       // Obtener vehículos desde detalles_cotizacion
       $vehiculos = VehiculoMantenimiento::whereIn('id', function ($query) use ($cotizacion) {
           $query->select('vehiculo_id')
                 ->from('detalles_cotizacion')
                 ->where('cotizacion_id', $cotizacion->id)
                 ->whereNotNull('vehiculo_id');
       })->get();
   
       // Fallback a todos los vehículos si no hay asociados
       if ($vehiculos->isEmpty()) {
           Log::warning('No se encontraron vehículos asociados', [
               'cotizacion_id' => $cotizacion->id
           ]);
           $vehiculos = VehiculoMantenimiento::all();
       }
   
       $clientes = Cliente::all();
       $tecnicos = User::role('tecnico')->get(); // Uso correcto de Spatie
       
       Log::info('Datos preparados para vista de índice', [
           'cotizacion_id' => $cotizacion->id,
           'vehiculos_count' => $vehiculos->count(),
           'tecnicos_count' => $tecnicos->count()
       ]);
       
       return view('admin.ventas.cotizaciones.proceso.orden-trabajo.index', 
                   compact('cotizacion', 'orden', 'vehiculos', 'clientes', 'tecnicos'));
   }
   
   public function create(Cotizacion $cotizacion)
   {
       Log::info('Iniciando creación de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id
       ]);

       // Obtener vehículos de la cotización
       $vehiculos = $cotizacion->vehiculos;
       
       Log::info('Vehículos de la cotización', [
           'cotizacion_id' => $cotizacion->id,
           'vehiculos_count' => $vehiculos->count()
       ]);
       
       // Si no hay vehículos en la relación, buscar en detalles de cotización
       if ($vehiculos->isEmpty()) {
           $vehiculos = [];
           foreach ($cotizacion->detalles as $detalle) {
               if ($detalle->vehiculo) {
                   $vehiculos[] = $detalle->vehiculo;
               }
           }
           
           // Fallback a todos los vehículos
           if (empty($vehiculos)) {
               Log::warning('No se encontraron vehículos, usando todos los vehículos', [
                   'cotizacion_id' => $cotizacion->id
               ]);
               $vehiculos = VehiculoMantenimiento::all();
           }
       }
       
       $tecnicos = User::role('tecnico')->get(); // Uso correcto de Spatie
       
       Log::info('Preparando vista de creación de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id,
           'vehiculos_count' => count($vehiculos),
           'tecnicos_count' => $tecnicos->count()
       ]);
       
       return view('admin.ventas.cotizaciones.proceso.orden-trabajo.modals.crear', 
                   compact('cotizacion', 'vehiculos', 'tecnicos'));
   }

   public function edit(Cotizacion $cotizacion, OrdenTrabajo $orden)
   {
       Log::info('Iniciando edición de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id,
           'orden_trabajo_id' => $orden->id
       ]);

       // Obtener vehículos asociados a la cotización
       $vehiculos = $cotizacion->vehiculos;
       
       if ($vehiculos->isEmpty()) {
           Log::warning('No se encontraron vehículos, usando todos los vehículos', [
               'cotizacion_id' => $cotizacion->id
           ]);
           $vehiculos = VehiculoMantenimiento::all();
       }
       
       $tecnicos = User::role('tecnico')->get(); // Uso correcto de Spatie
       
       Log::info('Preparando vista de edición de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id,
           'orden_trabajo_id' => $orden->id,
           'vehiculos_count' => $vehiculos->count(),
           'tecnicos_count' => $tecnicos->count()
       ]);
       
       return view('admin.ventas.cotizaciones.proceso.orden-trabajo.modals.editar', 
                   compact('cotizacion', 'orden', 'vehiculos', 'tecnicos'));
   }

   public function store(Request $request, Cotizacion $cotizacion)
   {
       Log::info('Iniciando proceso de creación de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id,
           'request_data' => $request->all()
       ]);
   
       try {
           // Buscar el detalle de la cotización con el vehículo
           $detalleCotizacion = $cotizacion->detalles->first();
   
           if (!$detalleCotizacion || !$detalleCotizacion->vehiculo) {
               Log::warning('No se encontró vehículo en la cotización', [
                   'cotizacion_id' => $cotizacion->id
               ]);
               
               return redirect()->back()->with('error', 'No se encontró un vehículo asociado a la cotización');
           }
   
           // Obtener el vehículo del catálogo
           $vehiculoCatalogo = $detalleCotizacion->vehiculo;
   
           // Crear o encontrar el vehículo de mantenimiento
           $vehiculoMantenimiento = VehiculoMantenimiento::firstOrCreate(
               ['vehiculo_id' => $vehiculoCatalogo->id]
           );
   
           Log::info('Vehículo de mantenimiento creado/encontrado', [
               'vehiculo_mantenimiento_id' => $vehiculoMantenimiento->id,
               'vehiculo_catalogo_id' => $vehiculoCatalogo->id
           ]);
   
           // Validar datos de la orden de trabajo
           $validated = $request->validate([
               'estado' => 'required',
               'descripcion' => 'required',
               'fecha_fin_estimada' => 'nullable|date',
               'tecnico_id' => 'nullable|exists:users,id'
           ]);
          
           // Preparar datos para la orden de trabajo
           $validated['fecha_inicio'] = now();
           $validated['cotizacion_id'] = $cotizacion->id;
           $validated['vehiculo_id'] = $vehiculoMantenimiento->id;
          
           // Crear la orden de trabajo
           $ordenTrabajo = OrdenTrabajo::create($validated);
   
           Log::info('Orden de trabajo creada', [
               'orden_trabajo_id' => $ordenTrabajo->id
           ]);
   
           // Crear la orden de trabajo de mantenimiento
           $ordenMantenimiento = OrdenTrabajoMantenimiento::create([
               'codigo_orden' => 'OTM-' . date('Ymd') . '-' . $ordenTrabajo->id,
               'vehiculo_id' => $vehiculoMantenimiento->id,
               'cliente_id' => $cotizacion->cliente_id,
               'fecha_ingreso' => now(),
               'fecha_inicio_trabajo' => now(),
               'kilometraje_ingreso' => 0,
               'descripcion_problema' => $ordenTrabajo->descripcion,
               'tecnico_asignado_id' => $validated['tecnico_id'] ?? null,
               'estado' => 'diagnostico'
           ]);
   
           Log::info('Orden de mantenimiento creada', [
               'orden_mantenimiento_id' => $ordenMantenimiento->id
           ]);
   
           return redirect()->route('admin.ventas.cotizaciones.orden-trabajo', $cotizacion)
                           ->with('success', 'Orden de trabajo creada exitosamente');
   
       } catch (\Exception $e) {
           Log::error('Error detallado al crear orden de trabajo', [
               'mensaje_error' => $e->getMessage(),
               'trace' => $e->getTraceAsString(),
               'request_data' => $request->all()
           ]);
           
           return redirect()->back()->with('error', 'No se pudo crear la orden de trabajo: ' . $e->getMessage());
       }
   }

   public function update(Request $request, Cotizacion $cotizacion, OrdenTrabajo $orden)
   {
       Log::info('Iniciando actualización de orden de trabajo', [
           'cotizacion_id' => $cotizacion->id,
           'orden_trabajo_id' => $orden->id,
           'request_data' => $request->all()
       ]);

       try {
           // Validar datos
           $validated = $request->validate([
               'estado' => 'required',
               'descripcion' => 'required',
               'fecha_fin_estimada' => 'nullable|date',
               'observaciones' => 'nullable|string',
               'tecnico_id' => 'nullable|exists:users,id'
           ]);
          
           // Actualizar la orden de trabajo
           $orden->update($validated);

           Log::info('Orden de trabajo actualizada', [
               'orden_trabajo_id' => $orden->id
           ]);
          
           return redirect()->route('admin.ventas.cotizaciones.orden-trabajo', $cotizacion)
                           ->with('success', 'Orden de trabajo actualizada exitosamente');

       } catch (\Exception $e) {
           Log::error('Error al actualizar orden de trabajo', [
               'mensaje_error' => $e->getMessage(),
               'trace' => $e->getTraceAsString(),
               'cotizacion_id' => $cotizacion->id,
               'orden_trabajo_id' => $orden->id
           ]);
           
           return redirect()->back()->with('error', 'No se pudo actualizar la orden de trabajo: ' . $e->getMessage());
       }
   }
}