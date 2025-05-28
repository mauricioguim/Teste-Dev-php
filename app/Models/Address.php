<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    /**
     * Fillable attributes for the address model.
     *
     * @var string[]
     */
    protected $fillable = [
        'client_id',
        'cep',
        'street',
        'neighborhood',
        'city',
        'state',
    ];

    /**
     * Get the client that owns the address.
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
