<?php

namespace App\Helpers\WorkSite;

interface IWorkSite {
    /**
     * @param string $query
     * @param object $options
     * @return array [
     *      site => string,
     *      link => string,
     *      title => string,
     *      description => string,
     *      salary => integer|null,
     *      date => string
     *]
     */
    public function search($query, $options);
}
