<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sneaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate', 'quantity'
    ];

    public function media() {
        return $this->hasOne(SneakerMedia::class);
    }
}
