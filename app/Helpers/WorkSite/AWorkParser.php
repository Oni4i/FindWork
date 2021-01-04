<?php

namespace App\Helpers\WorkSite;

use IvoPetkov\HTML5DOMDocument;

abstract class AWorkParser {

    protected $allowOptions;
    protected $options;
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
    public abstract function get($page = 0);
    public abstract function getAll();
    public abstract function set(HTML5DOMDocument $dom, array $options, string $url = null);
    protected function setOptions($options) {
        foreach ($options as $key => $value) {
            if (key_exists($key, $this->allowOptions)) {
                $this->options[$this->allowOptions[$key]] = $value;
            }
        }
    }
}
