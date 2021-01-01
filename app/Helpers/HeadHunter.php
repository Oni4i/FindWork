<?php

namespace App\Helpers;
use App\Helpers\WorkSite\IWorkSite;
use IvoPetkov\HTML5DOMDocument;

class HeadHunter implements IWorkSite {
    private static $url = 'https://hh.ru';

    public static function search($query, $options) {
        $html = new HTML5DOMDocument();
//        $html->loadHTMLFile(self::$url, LIBXML_NOERROR);
        return '';
    }
}
