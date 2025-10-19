<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'image'];

   

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id');
    }

    public function getImageAttribute($value)
    {
        return $value ? str_replace('https://motors.azsystems.tech', 'https://backend.motorssooq.com', $value) : null;
    }
}
