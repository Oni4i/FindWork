<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $guarded =['id'];

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
