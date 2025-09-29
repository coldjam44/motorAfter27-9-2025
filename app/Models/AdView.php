<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Userauth;
use App\Models\Ad;

class AdView extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ad_id','identifier'];

    public function user()
    {
        return $this->belongsTo(Userauth::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
