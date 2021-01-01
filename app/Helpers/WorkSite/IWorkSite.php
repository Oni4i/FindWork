<?php

namespace App\Helpers\WorkSite;

interface IWorkSite {
    /**
     * @param string $query
     * @param object $options
     * @return mixed
     */
    public function search($query, $options);
}
