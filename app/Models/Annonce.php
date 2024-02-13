<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'description',
        'etat',
        'type',
        'categorie_id',
        'localite_id',
        'date_limite',
        'user_id'
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function localite()
    {
        return $this->belongsTo(Localite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
