<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'cotizacion_id',
        'nombre',
        'categoria',
        'fecha',
        'archivo',
        'descripcion',
        'user_id'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    /**
     * Obtiene la cotización asociada al documento
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el usuario que creó el documento
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}