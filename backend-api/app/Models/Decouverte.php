<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decouverte extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'categorie',
        'description',
        'commune',
        'telephone',
        'images'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}