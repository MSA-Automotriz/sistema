<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioSeguimientoOrden extends Model
{
    use HasFactory;

    protected $table = 'comentarios_seguimiento_orden';

    protected $fillable = [
        'seguimiento_id',
        'user_id',
        'contenido',
        'archivo'
    ];

    // Relaciones
    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoOrdenTrabajo::class, 'seguimiento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accesor para determinar si el archivo es una imagen
    public function getEsImagenAttribute()
    {
        if (!$this->archivo) {
            return false;
        }

        $extension = pathinfo($this->archivo, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
    }

    // Accesor para obtener la ruta completa del archivo
    public function getRutaArchivoAttribute()
    {
        if (!$this->archivo) {
            return null;
        }

        return '/storage/' . $this->archivo;
    }

    // Accesor para obtener el nombre original del archivo
    public function getNombreArchivoAttribute()
    {
        if (!$this->archivo) {
            return null;
        }

        // Suponiendo que se guarda con formato timestamp_nombreoriginal.ext
        $nombreCompleto = pathinfo($this->archivo, PATHINFO_BASENAME);
        $partes = explode('_', $nombreCompleto, 2);
        
        return count($partes) > 1 ? $partes[1] : $nombreCompleto;
    }

    // Accesor para obtener la extensión del archivo
    public function getExtensionArchivoAttribute()
    {
        if (!$this->archivo) {
            return null;
        }

        return pathinfo($this->archivo, PATHINFO_EXTENSION);
    }

    // Scopes
    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}