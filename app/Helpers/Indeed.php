<?php

namespace App\Helpers;
use App\Helpers\WorkSite\IWorkSite;
use IvoPetkov\HTML5DOMDocument;

class Indeed implements IWorkSite {
    private static $countriesUrl = 'https://www.indeed.com/worldwide';

    public static function search($query, $options) {
        $html = new HTML5DOMDocument();

        $countries = self::getCountries($html);

        for ($i = 0; $i < sizeof($countries); $i++) {
            $countries[$i]['pages'] = self::getNumberOfPage($html, $query, $options, $countries[$i]['link']);
        }

        return $countries;
    }

    /**
     * @param string $query
     * @param array $options
     * @param null|integer $page
     * @return string
     */
    private static function getPreparedUrl($query, $options, $url, $page = null) {
        $params = [
            'q' => $query
        ];
        if ($page !== null) $params['start'] = $page * 10;
        return $url . '?' . http_build_query($params);
    }

    private static function getNumberOfPage(HTML5DOMDocument $html, $query, $options, $link, $page = 0) {
        $url = self::getPreparedUrl($query, $options, $link, $page);
        $html->loadHTMLFile($url, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);

        if ($html->querySelector('.pagination-list svg')) {
            $html = self::getNumberOfPage($html, $query, $options, $link, $page + 1);
        } else {
            return $html;
        }

        ///
    }

    /**
     * @return array
     */
    public static function getCountries(HTML5DOMDocument $html) {
        $html->loadHTMLFile(self::$countriesUrl, LIBXML_NOERROR);
        $countriesList = $html->querySelectorAll('.countriesContainer .countries li.countryItem');

        $countries = [];

        for ($i = 0; $i < sizeof($countriesList); $i++) {
            $temp = $countriesList[$i];
            $countryName = strtolower($temp->querySelector('span')->getTextContent());
            $a = $temp->querySelector('a');

            if ($a) $link = $a->getAttribute('href');
            else $link = 'www.indeed.com';

            $countries[] = ['name' => $countryName, 'link' => $link . '/jobs'];
        }

        return $countries;
    }
}
