<?php

namespace App\Http\Controllers\Find;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        //Compare
        if (!$request->options || !$request->options['sites']) {
            $validSites = $this->sites;
        } else {
            $validSites = array_intersect(array_keys($this->sites), $request->options['sites']);
            if (!$validSites) {
                return response()->json(['success' => 0, 'response' => 'Such sites not found']);
            }
        }

        //Call require site classes
        foreach ($validSites as $site) {
            $data[$site] = call_user_func_array(['App\Helpers\\' . $site, 'search'], [$request->query, $request->options]);
        }

        return response()->json($data);
    }
}
