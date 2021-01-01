<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function words() {
        return $this->belongsToMany(Word::class, 'word_language');
    }
}
