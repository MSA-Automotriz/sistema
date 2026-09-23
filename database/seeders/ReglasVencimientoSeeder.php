<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReglaVencimientoCotizacion;
use App\Models\EstadoCotizacion;

class ReglasVencimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar un estado para cotizaciones vencidas (o crear uno genérico)
        $estadoVencido = EstadoCotizacion::where('nombre', 'LIKE', '%vencid%')
            ->orWhere('nombre', 'LIKE', '%pendiente%')
            ->orWhere('nombre', 'LIKE', '%espera%')
            ->first();
            
        if (!$estadoVencido) {
            // Si no existe, tomar el primer estado disponible
            $estadoVencido = EstadoCotizacion::first();
        }
        
        if (!$estadoVencido) {
            $this->command->warn('No se encontraron estados de cotización. Creando estados básicos...');
            
            // Crear estados básicos
            EstadoCotizacion::create(['nombre' => 'Pendiente', 'descripcion' => 'Cotización pendiente de seguimiento', 'color' => 'warning']);
            EstadoCotizacion::create(['nombre' => 'En Proceso', 'descripcion' => 'Cotización en proceso de negociación', 'color' => 'primary']);
            EstadoCotizacion::create(['nombre' => 'Vencida', 'descripcion' => 'Cotización vencida por falta de seguimiento', 'color' => 'danger']);
            EstadoCotizacion::create(['nombre' => 'Cerrado Ganado', 'descripcion' => 'Cotización ganada', 'color' => 'success']);
            EstadoCotizacion::create(['nombre' => 'Cerrado Perdido', 'descripcion' => 'Cotización perdida', 'color' => 'secondary']);
            
            $estadoVencido = EstadoCotizacion::where('nombre', 'Vencida')->first();
        }

        // Crear reglas de ejemplo
        $reglas = [
            [
                'nombre' => 'Vendedores Junior',
                'descripcion' => 'Regla para vendedores con poca experiencia. Cotizaciones vencen rápidamente para evitar pérdida de oportunidades.',
                'dias_vencimiento' => 7,
                'dias_alerta' => 2,
                'estado_vencido_id' => $estadoVencido->id,
                'permite_reasignacion' => true,
                'requiere_aprobacion' => false,
                'notificar_vencimiento' => true,
                'activo' => true,
                'condiciones' => null
            ],
            [
                'nombre' => 'Vendedores Senior',
                'descripcion' => 'Regla para vendedores experimentados. Más tiempo para gestionar cotizaciones complejas.',
                'dias_vencimiento' => 15,
                'dias_alerta' => 3,
                'estado_vencido_id' => $estadoVencido->id,
                'permite_reasignacion' => true,
                'requiere_aprobacion' => true,
                'notificar_vencimiento' => true,
                'activo' => true,
                'condiciones' => null
            ],
            [
                'nombre' => 'Cotizaciones de Alto Valor',
                'descripcion' => 'Para cotizaciones importantes que requieren más tiempo de negociación.',
                'dias_vencimiento' => 30,
                'dias_alerta' => 5,
                'estado_vencido_id' => $estadoVencido->id,
                'permite_reasignacion' => true,
                'requiere_aprobacion' => true,
                'notificar_vencimiento' => true,
                'activo' => false, // Inactiva por defecto
                'condiciones' => null
            ],
            [
                'nombre' => 'Regla Estándar',
                'descripcion' => 'Regla por defecto para la mayoría de cotizaciones.',
                'dias_vencimiento' => 10,
                'dias_alerta' => 2,
                'estado_vencido_id' => $estadoVencido->id,
                'permite_reasignacion' => true,
                'requiere_aprobacion' => false,
                'notificar_vencimiento' => true,
                'activo' => true,
                'condiciones' => null
            ]
        ];

        foreach ($reglas as $reglaData) {
            if (!ReglaVencimientoCotizacion::where('nombre', $reglaData['nombre'])->exists()) {
                ReglaVencimientoCotizacion::create($reglaData);
                $this->command->info("Regla '{$reglaData['nombre']}' creada exitosamente.");
            } else {
                $this->command->warn("Regla '{$reglaData['nombre']}' ya existe, omitiendo...");
            }
        }

        $this->command->info('Seeder de reglas de vencimiento completado exitosamente.');
    }
}
