<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{

    protected $fillable = ['foreign_name', 'local_name', 'language_id'];

    public function languages() {
        return $this->belongsToMany(Language::class, 'word_language');
    }
}
