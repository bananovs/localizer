<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localize extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function locItem()
    {
        return $this->hasMany(LocItem::class,'localize_id', 'id');
    }


}
