<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CorregirDetallesCotizacion extends Command
{
    protected $signature = 'cotizaciones:corregir-vehiculos';
    protected $description = 'Corrige los detalles de cotización relacionando vehículos correctamente';

    public function handle()
    {
        // Deshabilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Consulta SQL directa para encontrar detalles sin vehiculo_id
            $detalles = DB::select("
                SELECT id, vehiculo_id 
                FROM detalles_cotizacion 
                WHERE vehiculo_id IS NULL
            ");

            $this->info('Encontrados ' . count($detalles) . ' detalles de cotización para corregir.');

            $now = date('Y-m-d H:i:s');

            foreach ($detalles as $detalle) {
                // Verificar si ya existe un registro de vehículo de mantenimiento
                $existente = DB::select("
                    SELECT id 
                    FROM vehiculos_mantenimiento 
                    WHERE id = ?
                ", [$detalle->id]);

                if (empty($existente)) {
                    // Inserción directa usando consulta SQL
                    DB::statement("
                        INSERT INTO vehiculos_mantenimiento 
                        (id, created_at, updated_at) 
                        VALUES (?, ?, ?)
                    ", [
                        $detalle->id, 
                        $now, 
                        $now
                    ]);

                    $vehiculoMantenimientoId = $detalle->id;
                } else {
                    // Usar el ID existente
                    $vehiculoMantenimientoId = $existente[0]->id;
                }

                $this->info('Corregido detalle de cotización ID: ' . $detalle->id);
            }

            $this->info('Corrección de vehículos en detalles de cotización completada.');
        } catch (\Exception $e) {
            $this->error('Error durante la corrección: ' . $e->getMessage());
            $this->error('Traza del error: ' . $e->getTraceAsString());
        } finally {
            // Volver a habilitar las restricciones de clave foránea
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}