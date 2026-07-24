<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory; // <-- LA MAGIE EST ICI

    protected $fillable = [
        'user_id', // <-- Ajout indispensable
        'titre', 
        'description', 
        'prix', 
        'commune', 
        'ville',
        'type_contrat',   
        'images'         
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}