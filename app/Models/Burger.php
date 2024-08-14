<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Burger extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'prix', 'image', 'description', 'archived'];

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
