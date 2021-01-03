<?php

use IvoPetkov\HTML5DOMDocument;

class HeadHunter {

    private $url = 'https://hh.ru/search/vacancy?';
    private $DOM;

    private $defaultOptions = [
        'L_is_autosearch' => 'false',
        'enable_snippets' => 'true',
        'clusters' => 'true',
        'st' => 'searchVacancy'
    ];
    private $allowOptions = [
        'query',
        'salary',
        'area'
    ];
    private $options = [];

    public function __construct(HTML5DOMDocument $dom, array $options, string $url = null) {
        if (!$url) $this->url = $url;
        $this->DOM = $dom;
        $this->setOptions($options);
    }

    public function get($page = 0) {
        $url = $this->getSearchUrl($page);
        $html = $this->DOM->loadHTMLFile($url);

        if ($this->isError($html)) return null;
        $data = $this->getVacancies($html);

        return $data;
    }

    private function getVacancies($html) {
        $selector = $html->querySelectorAll('.vacancy-serp-item.HH-VacancySidebarTrigger-Vacancy');
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
        return isset($vacancies) ? $vacancies : null;
    }

    private function isError($html) {
        return $html->querySelector('div.error div.error__content') ? true : false;
    }

    private function getSearchUrl($page) {
        return $this->url . http_build_query($this->options + ['page' => $page]);
    }

    private function setOptions($options) {
        foreach ($options as $key => $value) {
            if (in_array($key, $this->allowOptions)) {
                $this->options[$key] = $value;
            }
        }
    }
}
