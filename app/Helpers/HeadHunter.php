<?php

namespace App\Helpers;
use App\Helpers\WorkSite\AWorkParser;
use IvoPetkov\HTML5DOMDocument;

class HeadHunter extends AWorkParser {

    private $url = 'https://hh.ru/search/vacancy?';
    private $DOM;

    private $defaultOptions = [
        'L_is_autosearch' => 'false',
        'enable_snippets' => 'true',
        'clusters' => 'true',
        'st' => 'searchVacancy'
    ];
    protected $allowOptions = [
        'query' =>'text',
        'salary' => 'salary',
    ];

    private $countries = [];
    protected $options = [];

    public function set(HTML5DOMDocument $dom, array $options, string $url = null) {
        if ($url) $this->url = $url;
        $this->DOM = $dom;
        $this->setOptions($options);
        $this->countries = $this->getCodesCountries($options['countries']);
    }

    public function get($page = 0) {
//        $url = $this->getSearchUrl($page);
//        $html = $this->DOM->loadHTMLFile($url);
//
//        if ($this->isError($html)) return null;
//        $data = $this->getVacancies($html);
//
//        return $data;
    }

    public function getAll() {
        $data = [];
        for ($i = 0; $i < sizeof($this->countries); $i++) {
            $country = $this->countries[$i];
            $page = 0;
            while (true) {
                $url = $this->getSearchUrl($page, $country);
                $this->DOM->loadHTMLFile($url, LIBXML_NOERROR);
                if ($this->isError($this->DOM)) break;
                $data = array_merge($data, $this->getVacancies($this->DOM));
                $page++;
            }
        }
        return $data;
    }

    private function getVacancies($html) {
        $selector = $html->querySelectorAll('.vacancy-serp-item.HH-VacancySidebarTrigger-Vacancy');
        $vacancies = [];
        for ($i = 0; $i < sizeof($selector); $i++) {
            $temp = $selector[$i];
            $vacancy['title'] = $temp->querySelector('.vacancy-serp-item__info .g-user-content a')->getTextContent();
            $vacancy['link'] = $temp->querySelector('.vacancy-serp-item__info .g-user-content a')->getAttribute('href');
            $vacancy['link'] = explode('?', str_replace('//', '/', $vacancy['link']))[0];
            $vacancy['city'] = $temp->querySelector('span[data-qa="vacancy-serp__vacancy-address"]')->getTextContent();
            $vacancy['description'] = $temp->querySelector('div[data-qa="vacancy-serp__vacancy_snippet_responsibility"]')->getTextContent();

            $vacancy['company'] = $temp->querySelector('a[data-qa="vacancy-serp__vacancy-employer"]');
            if ($vacancy['company']) {
                $vacancy['company'] = trim($vacancy['company']->getTextContent());
            }

            $vacancy['salary'] = $temp->querySelector('span[data-qa="vacancy-serp__vacancy-compensation"]');
            if ($vacancy['salary']) {
                $vacancy['salary'] = $vacancy['salary']->getTextContent();
            }
            $vacancies[] = $vacancy;
        }
        return $vacancies;
    }

    private function isError($html) {
        return sizeof($html->querySelectorAll('.vacancy-serp-item.HH-VacancySidebarTrigger-Vacancy')) ? false : true;
    }

    private function getSearchUrl($page, $country) {
        return $this->url . http_build_query(
            $this->options +
            $this->defaultOptions +
            ['page' => $page, 'area' => $country]
            );
    }

    private function getCodesCountries($countries) {
        $areas = json_decode(file_get_contents(asset('json/countries.json')));
        $areas = array_filter($areas, function ($country) use ($countries) {
            foreach ($countries as $countryName) {
                if (strtolower($countryName) == $country->name) return true;
            }
            return false;
        });
        $areas = array_map(function ($area) {
            return $area->id;
        }, $areas);
        return $areas;
    }
}
