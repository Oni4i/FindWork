<?php

namespace App\Helpers;
use App\Helpers\WorkSite\IWorkSite;

class Indeed implements IWorkSite {
    public static function search($query, $options)
    {
        return 'Indeed data';
    }
}
