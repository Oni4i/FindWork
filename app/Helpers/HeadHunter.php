<?php

namespace App\Helpers;
use App\Helpers\WorkSite\IWorkSite;
use IvoPetkov\HTML5DOMDocument;

class HeadHunter implements IWorkSite {
    private static $url = 'https://hh.ru/search/vacancy?';

    private static $options = [

    ];

    private static $defaultOptions = [
        'L_is_autosearch' => 'false',
        'clusters' => 'true',
        'enable_snippets' => 'true',
        'st' => 'searchVacancy'
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
            $pages[$code->id]['pages'] = self::getNumberOfPages($url);
            $pages[$code->id]['name'] = $code->name;
        }

        $urls =[];
        foreach ($pages as $code => $value) {
            for ($i = 0; $i < $value['pages'] || ($value['pages'] === 0 && $i === 0); $i++) {
                $urls[$code][] = self::getPreparedUrl($query, $code, $options, $i);
            }
        }

        $result = [];
        foreach ($urls as $code => $codeUrls) {
            foreach ($codeUrls as $url) {
//                $result[$code][] = self::getVacancies($url);
                $result = array_merge($result, self::getVacancies($url));
            }
        }

        return $result;
    }

    private static function getVacancies($url) {
        $html = new HTML5DOMDocument();
        $html->loadHTMLFile($url, LIBXML_NOERROR);
        $selector = $html->querySelectorAll('.vacancy-serp-item.HH-VacancySidebarTrigger-Vacancy');

        if (!$selector) return null;

        $vacancies = [];
        foreach ($selector as $vacancyHtml) {
            $vacancy['title'] = $vacancyHtml->querySelector('.vacancy-serp-item__info .g-user-content a')->getTextContent();
            $vacancy['link'] = $vacancyHtml->querySelector('.vacancy-serp-item__info .g-user-content a')->getAttribute('href');
            $vacancy['link'] = explode('?', str_replace('//', '/', $vacancy['link']))[0];
            $vacancy['city'] = $vacancyHtml->querySelector('span[data-qa="vacancy-serp__vacancy-address"]')->getTextContent();
            $vacancy['description'] = $vacancyHtml->querySelector('div[data-qa="vacancy-serp__vacancy_snippet_responsibility"]')->getTextContent();

            $vacancy['company'] = $vacancyHtml->querySelector('a[data-qa="vacancy-serp__vacancy-employer"]');
            if ($vacancy['company']) {
                $vacancy['company'] = trim($vacancy['company']->getTextContent());
            }

            $vacancy['salary'] = $vacancyHtml->querySelector('span[data-qa="vacancy-serp__vacancy-compensation"]');
            if ($vacancy['salary']) {
                $vacancy['salary'] = $vacancy['salary']->getTextContent();
            }

            $vacancies[] = $vacancy;
        }

        return $vacancies;
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

    private static function getPreparedUrl($query, $code, $options, $page = null) {
        $params = [
            'text' => $query,
            'area' => $code
        ] + self::$defaultOptions;
        if ($page !== null) $params['page'] = $page;
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
