<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['photoed_id', 'photoed_type', 'order', 'title', 'url', 'file_name', 'description', 'width', 'height', 'size'];

    public function photoed()
    {
        return $this->morphTo();
    }
}
