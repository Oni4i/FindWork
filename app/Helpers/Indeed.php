<?php

namespace App\Helpers;
use App\Helpers\WorkSite\AWorkParser;
use IvoPetkov\HTML5DOMDocument;

class Indeed extends AWorkParser {

    private $countriesUrl = 'https://www.indeed.com/worldwide';
    private $DOM;

    private $defaultOptions = [
    ];
    protected $allowOptions = [
        'query' =>'q'
    ];

    private $countries = [];
    protected $options = [];

    public function set(HTML5DOMDocument $dom, array $options, string $url = null) {
        if ($url) $this->url = $url;
        $this->DOM = $dom;
        $this->setOptions($options);
        $this->countries = $this->getCountriesLinks($options['countries']);
    }

    public function get($page = 0) {

    }

    public function getAll() {
        $data = [];
        for ($i = 0; $i < sizeof($this->countries); $i++) {
            $country = $this->countries[$i];
            $page = 0;
            while (true) {
                $url = $this->getSearchUrl($country['link'], $page);
                $this->DOM->loadHTMLFile($url, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                if ($this->isError($this->DOM)) break;
                $data = array_merge($data, $this->getVacancies($this->DOM));
                $page++;
            }
        }
        return $data;
    }

    /**
     * @param string $query
     * @param array $options
     * @param null|integer $page
     * @return string
     */
    private function getSearchUrl($url, $page) {
        return $url . http_build_query(
                $this->options +
                $this->defaultOptions +
                ['start' => $page * 10]
            );
    }

    private function getVacancies($html) {
        return [];
    }

    /**
     * @return array
     */
    private function getAllCountriesLinks() {
        $this->DOM->loadHTMLFile($this->countriesUrl, LIBXML_NOERROR);
        $countriesList = $this->DOM->querySelectorAll('.countriesContainer .countries li.countryItem');

        $countries = [];

        for ($i = 0; $i < sizeof($countriesList); $i++) {
            $temp = $countriesList[$i];
            $countryName = strtolower($temp->querySelector('span')->getTextContent());
            $a = $temp->querySelector('a');

            if ($a) $link = $a->getAttribute('href');
            else $link = 'www.indeed.com';

            $countries[] = ['name' => $countryName, 'link' => $link . '/jobs?'];
        }

        return $countries;
    }

    private function getCountriesLinks(array $countries) {
        $allCountries = $this->getAllCountriesLinks();
        $countries = array_map('strtolower', $countries);
        $foundCountries = [];
        for ($i = 0; $i < sizeof($allCountries); $i++) {
            if (in_array($allCountries[$i]['name'], $countries)) {
                $foundCountries[] = $allCountries[$i];
            }
        }

        return $foundCountries;
    }

    private function isError($html) {
        return sizeof($html->querySelectorAll('.vacancy-serp-item.HH-VacancySidebarTrigger-Vacancy')) ? false : true;
    }
}
