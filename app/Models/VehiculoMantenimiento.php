<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculoMantenimiento extends Model
{
    use HasFactory;
    
    // Verifica que el nombre de la tabla sea correcto
    protected $table = 'vehiculos_mantenimiento';
    
    // Si la tabla no tiene timestamps estándar de Laravel (created_at, updated_at)
    public $timestamps = true; // Descomenta esta línea si tu tabla no tiene timestamps
    
    protected $fillable = [
        'vehiculo_id',
        'marca_id',
        'modelo_id',
        'anio',
        'color',
        'nro_placa',
        'serie_vim',
        'motor',
        'combustible_id',
        'kilometraje',
        'cliente_id',
        'datos_propietario',
        'ruc_dni_propietario',
    ];
    
    // Relaciones con verificación de existencia
    public function vehiculoOrigen()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id')->withDefault([
            'nombre' => 'Sin Vehículo'
        ]);
    }
    
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id')->withDefault([
            'nombre' => 'Sin Marca'
        ]);
    }
   
    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id')->withDefault([
            'nombre' => 'Sin Modelo'
        ]);
    }
   
    public function combustible()
    {
        return $this->belongsTo(Combustible::class, 'combustible_id')->withDefault([
            'nombre' => 'Sin Combustible'
        ]);
    }
   
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id')->withDefault([
            'nombres' => 'Sin Cliente',
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'razon_social' => 'Sin Cliente'
        ]);
    }
   
    public function citas()
    {
        return $this->hasMany(CitaMantenimiento::class, 'vehiculo_id');
    }
   
    public function ordenesTrabajoMantenimiento()
    {
        return $this->hasMany(OrdenTrabajoMantenimiento::class, 'vehiculo_id');
    }
   
    public function historialMantenimiento()
    {
        return $this->ordenesTrabajoMantenimiento()->orderBy('created_at', 'desc');
    }
   
    public function detalleCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class, 'vehiculo_id');
    }
    
    // Método para obtener el kilometraje actual
    public function getKilometrajeActual()
    {
        return $this->kilometraje ?? 0;
    }
    
    // Método para actualizar el kilometraje
    public function actualizarKilometraje($nuevoKilometraje)
    {
        // Verifica que el nuevo kilometraje sea mayor que el actual
        if ($nuevoKilometraje > $this->kilometraje) {
            $this->kilometraje = $nuevoKilometraje;
            $this->save();
            return true;
        }
        return false;
    }
}