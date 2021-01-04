<?php

namespace App\Http\Controllers\Find;

use App\Helpers\WorkSite\IWorkParser;
use App\Helpers\WorkSiteHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use IvoPetkov\HTML5DOMDocument;

class FindController extends Controller
{
    private $sites = [
        'hh' => 'HeadHunter',
        'indeed' => 'Indeed'
    ];

    public function index() {
        return view('find.index');
    }

    public function search(Request $request) {
        $workSites = WorkSiteHelper::getParsers($request->options['sites']);
        $dom = new HTML5DOMDocument();

        $data = WorkSiteHelper::getData($dom, $request, $workSites);
        $links = Auth::user()->vacanciesLink();
        $data = WorkSiteHelper::setFavourite($links, $data);

        return response()
                        ->json(['success' => 1, 'response' => $data], '200', [], JSON_UNESCAPED_UNICODE);
    }

//    public function search(Request $request) {
//        ini_set('max_execution_time', 100000);
//        //Compare
//        if (!$request->has('options') || !isset($request->options['sites'])) {
//            $validSites = array_keys($this->sites);
//        } else {
//            $validSites = array_intersect(array_keys($this->sites), $request->options['sites']);
//            if (!$validSites) {
//                return response()
//                    ->json(['success' => 0, 'response' => 'Such sites not found']);
//            }
//        }
//
//        //Call require site classes
//        foreach ($validSites as $site) {
//            $data[$site] = call_user_func_array(
//                ['App\Helpers\\' . $this->sites[$site], 'search'],
//                [$request->input('query'), $request->options]
//            );
//        }
//
//        $links = Auth::user()->vacanciesLink();
//        foreach ($data as &$site) {
//            if (!is_array($site)) continue;
//            foreach ($site as &$info) {
//                if ($links->contains($info['link'])) {
//                    $info['isFavourite'] = 1;
//                } else {
//                    $info['isFavourite'] = 0;
//                }
//            }
//        }
//
//        return response()
//            ->json(['success' => 1, 'response' => $data], '200', [], JSON_UNESCAPED_UNICODE);
//    }
}
