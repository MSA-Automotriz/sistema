<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cotizacion;
use App\Models\ReglaVencimientoCotizacion;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcesarVencimientosCotizaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cotizaciones:procesar-vencimientos 
                          {--dry-run : Ejecutar en modo simulación sin hacer cambios}
                          {--verbose : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa las cotizaciones vencidas según las reglas configuradas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $verbose = $this->option('verbose');
        
        $this->info('🕐 Iniciando procesamiento de vencimientos de cotizaciones...');
        
        if ($dryRun) {
            $this->warn('⚠️  Modo simulación activado - no se realizarán cambios');
        }
        
        // Obtener cotizaciones que necesitan ser procesadas
        $cotizacionesPorVencer = $this->obtenerCotizacionesParaProcesar();
        
        if ($cotizacionesPorVencer->isEmpty()) {
            $this->info('✅ No hay cotizaciones que requieran procesamiento');
            return 0;
        }
        
        $this->info("📋 Encontradas {$cotizacionesPorVencer->count()} cotizaciones para procesar");
        
        $procesadas = 0;
        $errores = 0;
        
        foreach ($cotizacionesPorVencer as $cotizacion) {
            try {
                $resultado = $this->procesarCotizacion($cotizacion, $dryRun, $verbose);
                
                if ($resultado) {
                    $procesadas++;
                    if ($verbose) {
                        $this->line("✅ Cotización #{$cotizacion->codigo} procesada correctamente");
                    }
                } else {
                    if ($verbose) {
                        $this->line("⏭️  Cotización #{$cotizacion->codigo} no requiere acción");
                    }
                }
            } catch (\Exception $e) {
                $errores++;
                $this->error("❌ Error procesando cotización #{$cotizacion->codigo}: {$e->getMessage()}");
                Log::error("Error procesando vencimiento cotización {$cotizacion->id}: {$e->getMessage()}");
            }
        }
        
        // Resumen final
        $this->newLine();
        $this->info("📊 Resumen del procesamiento:");
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Cotizaciones encontradas', $cotizacionesPorVencer->count()],
                ['Cotizaciones procesadas', $procesadas],
                ['Errores', $errores],
            ]
        );
        
        if ($errores > 0) {
            $this->error("⚠️  Se encontraron {$errores} errores. Revisa los logs para más detalles.");
            return 1;
        }
        
        $this->info('✅ Procesamiento completado exitosamente');
        return 0;
    }
    
    /**
     * Obtiene las cotizaciones que necesitan ser procesadas
     */
    private function obtenerCotizacionesParaProcesar()
    {
        return Cotizacion::with(['reglaVencimiento', 'estado', 'usuario', 'cliente'])
            ->whereNotNull('regla_vencimiento_id')
            ->where('vencida', false)
            ->where(function($query) {
                // Cotizaciones que ya pasaron su fecha de vencimiento
                $query->where('fecha_vencimiento', '<=', now())
                      // O cotizaciones que necesitan alerta
                      ->orWhere('fecha_alerta', '<=', now());
            })
            ->get();
    }
    
    /**
     * Procesa una cotización individual
     */
    private function procesarCotizacion(Cotizacion $cotizacion, bool $dryRun, bool $verbose): bool
    {
        $ahora = now();
        $accionRealizada = false;
        
        // Verificar si necesita alerta
        if ($cotizacion->fecha_alerta && 
            $cotizacion->fecha_alerta <= $ahora && 
            $cotizacion->fecha_vencimiento > $ahora) {
            
            if ($verbose) {
                $dias = $ahora->diffInDays($cotizacion->fecha_vencimiento);
                $this->warn("⚠️  Cotización #{$cotizacion->codigo} próxima a vencer ({$dias} días)");
            }
            
            if (!$dryRun) {
                $this->enviarAlertaProximoVencimiento($cotizacion);
            }
            
            $accionRealizada = true;
        }
        
        // Verificar si ya venció
        if ($cotizacion->fecha_vencimiento && $cotizacion->fecha_vencimiento <= $ahora) {
            if ($verbose) {
                $diasVencida = $ahora->diffInDays($cotizacion->fecha_vencimiento);
                $this->error("⏰ Cotización #{$cotizacion->codigo} vencida hace {$diasVencida} días");
            }
            
            if (!$dryRun) {
                $cotizacion->marcarComoVencida();
                
                if ($cotizacion->reglaVencimiento->notificar_vencimiento) {
                    $this->enviarNotificacionVencimiento($cotizacion);
                }
            }
            
            $accionRealizada = true;
        }
        
        return $accionRealizada;
    }
    
    /**
     * Envía alerta de próximo vencimiento
     */
    private function enviarAlertaProximoVencimiento(Cotizacion $cotizacion)
    {
        // Aquí implementarías la lógica de notificación
        // Por ejemplo, enviar email, notificación push, etc.
        Log::info("Alerta de próximo vencimiento enviada", [
            'cotizacion_id' => $cotizacion->id,
            'codigo' => $cotizacion->codigo,
            'usuario_id' => $cotizacion->user_id,
            'cliente' => $cotizacion->cliente->nombres ?? $cotizacion->cliente->razon_social
        ]);
    }
    
    /**
     * Envía notificación de vencimiento
     */
    private function enviarNotificacionVencimiento(Cotizacion $cotizacion)
    {
        // Aquí implementarías la lógica de notificación
        Log::info("Notificación de vencimiento enviada", [
            'cotizacion_id' => $cotizacion->id,
            'codigo' => $cotizacion->codigo,
            'usuario_id' => $cotizacion->user_id,
            'cliente' => $cotizacion->cliente->nombres ?? $cotizacion->cliente->razon_social,
            'reasignable' => $cotizacion->reasignable
        ]);
    }
}
