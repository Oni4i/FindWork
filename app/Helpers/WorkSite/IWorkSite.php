<?php

namespace App\Helpers\WorkSite;

interface IWorkSite {
    /**
     * @param string $query
     * @param array $options
     * @return array [
     *      site => string,
     *      link => string,
     *      title => string,
     *      description => string,
     *      salary => integer|null,
     *      company => integer|null
     *]
     */
    public static function search($query, $options);
}
