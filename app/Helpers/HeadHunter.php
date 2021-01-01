<?php

namespace App\Helpers;
use App\Helpers\WorkSite\IWorkSite;
use IvoPetkov\HTML5DOMDocument;

class HeadHunter implements IWorkSite {
    private static $url = 'https://hh.ru/search/vacancy?';

    private static $options = [

    ];

    public static function search($query, $options) {
        $html = new HTML5DOMDocument();
        $html->loadHTMLFile(self::$url, LIBXML_NOERROR);
        return self::getAllCountriesData($query, $options);
    }

    private static function getAllCountriesData($query, $options) {
        if (!$options || !$options['countries']) $options['countries'] = [];
        $codes = self::getCodesByCountries($options['countries']);

        if (!$codes) return [];

        foreach ($codes as $code) {
            $url = self::getPreparedUrl($query, $code->id, $options);
            $pages[$code->name] = self::getNumberOfPages($url);
        }
        return $pages;
    }

    private static function getUrls($query, $options) {

    }

    private static function getNumberOfPages($url) {
        $html = new HTML5DOMDocument();
        $html->loadHTMLFile($url, LIBXML_NOERROR);
        $selector = $html->querySelectorAll('a.bloko-button.HH-Pager-Control');
        if (!$selector || count($selector) <= 2) {
            return 0;
        }
        return (int)$selector[count($selector) - 2]->getTextContent();
    }

    private static function getPreparedUrl($query, $code, $options) {
        $params = [
            'text' => $query,
            'area' => $code,
            'L_is_autosearch' => 'false',
            'clusters' => 'true',
            'enable_snippets' => 'true',
            'st' => 'searchVacancy'
        ];
        foreach ($options as $name => $value) {
            if (in_array($name, array_keys(self::$options))) {
                $params[self::$options[$name]] = $value;
            }
        }
        return self::$url . http_build_query($params);
    }

    private static function getCodesByCountries($countries) {
        $codesJSON = self::getCodesJSON();
        if ($countries) {
            $codes = array_filter($codesJSON, function ($country) use ($countries) {
                foreach ($countries as $countryName) {
                    if ($countryName == $country->name) return true;
                }
                return false;
            });
        } else {
            $codes = $codesJSON;
        }
        return array_chunk($codes, count($codes))[0];
    }

    private static function getCodesJSON() {
        return json_decode(file_get_contents(asset('json/countries.json')));
    }
}
