<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Boleto;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'documento',
        'email',
        'created_at',
        'updated_at'
    ];

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class);
    }

    public function scopeByDocumento($query, $documento)
    {
        $query->where('documento', $documento);
    }
}
