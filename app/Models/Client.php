<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    /**
     * Fillable attributes for the client model.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'cpf',
        'email',
        'phone',
    ];

    /**
     * Get the address associated with the client.
     *
     * @return HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}
