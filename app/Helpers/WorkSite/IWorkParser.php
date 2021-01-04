<?php

namespace App\Helpers\WorkSite;

use IvoPetkov\HTML5DOMDocument;

interface IWorkParser {
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
    public function get($page = 0);
    public function getAll();
    public function set(HTML5DOMDocument $dom, array $options, string $url = null);
}
