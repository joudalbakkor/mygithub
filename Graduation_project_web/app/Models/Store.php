<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;


    protected $fillable = [
        'name', 
        'type',
        'address', 
        'phone',
        'facebook_link',
        'instagram_link',
        'rating',
        'category_id',
        'seller_id',
        'product_id',
        'external_service_id',
        'image',
        
    ];


    
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

   public function externalService()
    {
        return $this->hasOne(ExternalService::class, 'id', 'external_service_id');
    }
}

