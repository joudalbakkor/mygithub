<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'price',
        'description',
        'commission',
        'image1',
        'image2',
        'image3',
        'image4',
        
    ];


    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}
