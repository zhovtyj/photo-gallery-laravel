<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'description', 'active'];

    public function photos()
    {
        return $this->morphMany('App\Photo', 'photoed')->orderBy('order', 'asc');
    }
}
