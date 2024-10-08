<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boleto extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'cliente_id',
        'valor',
        'data_vencimento',
        'created_at',
        'data_envio_email'
    ];

    protected $casts = [
        'data_vencimento' => 'datetime',
        'data_envio_email' => 'datetime',
    ];

    public function cliente(): BelongsTo {
        return $this->belongsTo(Cliente::class);
    }

    public function scopeByUuid($query, $uuid)
    {
        $query->where('uuid', $uuid);
    }

    public function scopeByCliente($query, $cliente_id)
    {
        $query->where('cliente_id', $cliente_id);
    }
}
