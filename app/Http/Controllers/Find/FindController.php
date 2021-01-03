<?php

namespace App\Http\Controllers\Find;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ini_set('max_execution_time', 100000);
        //Compare
        if (!$request->has('options') || !isset($request->options['sites'])) {
            $validSites = array_keys($this->sites);
        } else {
            $validSites = array_intersect(array_keys($this->sites), $request->options['sites']);
            if (!$validSites) {
                return response()->json(['success' => 0, 'response' => 'Such sites not found']);
            }
        }

        //Call require site classes
        foreach ($validSites as $site) {
            $data[$site] = call_user_func_array(
                ['App\Helpers\\' . $this->sites[$site], 'search'],
                [$request->input('query'), $request->options]
            );
        }

        $links = Auth::user()->vacancies()->with('vacancies')->pluck('link');
        foreach ($data as &$site) {
            if (!is_array($site)) continue;
            foreach ($site as &$info) {
                if ($links->contains($info['link'])) {
                    $info['isFavourite'] = 1;
                } else {
                    $info['isFavourite'] = 0;
                }
            }
        }

        return response()->json(['success' => 1, 'response' => $data], '200', [], JSON_UNESCAPED_UNICODE);
    }
}
