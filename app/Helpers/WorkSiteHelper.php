<?php

namespace App\Helpers;
use App\Helpers\WorkSite\AWorkParser;
use Illuminate\Http\Request;
use IvoPetkov\HTML5DOMDocument;

class WorkSiteHelper {
    /**
     * @param array $sites
     * return IWorkParser[] $parsers
     */
    public static function getParsers(array $sites) {
        $parsers = [];
        for ($i = 0; $i < sizeof($sites); $i++) {
            $site = $sites[$i];
            if ($site === 'hh') $parsers[] = new HeadHunter();
            else if ($site === 'indeed') $parsers[] = new Indeed();
        }
        return $parsers;
    }

    public static function getData(HTML5DOMDocument $dom, Request $request, array $workSites) {
        $data = array_map(function (AWorkParser $parser) use ($dom, $request) {
            $parser->set($dom, $request->options);
            return $parser->getAll();
        }, $workSites);

        $vacancies = [];
        for ($i = 0; $i < sizeof($data); $i++) {
            $vacancies = array_merge($vacancies, $data[$i]);
        }

        return $vacancies;
    }

    public static function setFavourite($links, $data) {
        foreach ($data as &$site) {
            if ($links->contains($site['link'])) {
                $info['isFavourite'] = 1;
            } else {
                $info['isFavourite'] = 0;
            }
        }
        return $data;
    }
}
