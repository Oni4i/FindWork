<?php

namespace App\Helpers\WorkSite;

interface IWorkSite {
    /**
     * @param string $query
     * @param object $options
     * @return array [
     *  array [
     *      site => string,
     *      link => string,
     *      title => string,
     *      description => string,
     *      salary => integer|null,
     *      company => integer|null
     * ]
     *]
     */
    public static function search($query, $options);
}
