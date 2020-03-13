<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'date',
        'description'
    ];

    public function setDescriptionAttribute($value) {
        $this->attributes['description'] = ucfirst($value);
    }
}
