<?php

namespace App\Helpers;
use App\Helpers\WorkSite\IWorkSite;

class HeadHunter implements IWorkSite {
    public static function search($query, $options)
    {
        return 'HeadHunter data';
    }
}
