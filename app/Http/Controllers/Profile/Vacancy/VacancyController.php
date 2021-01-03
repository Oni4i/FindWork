<?php

namespace App\Http\Controllers\Profile\Vacancy;

use App\Http\Controllers\Controller;
use App\User;
use App\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vacancies = User::query()->with('vacancies')->find(Auth::user()->id)->vacancies()->get();
        return view('profile.vacancy.index', compact('vacancies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:1',
            'location' => 'required|string|min:1',
            'site' => 'required|string|min:1',
            'salary' => 'string|nullable',
            'company' => 'string|nullable',
            'description' => 'string|nullable',
            'link' => 'required|string|min:20',
        ]);

        $vacancy = Vacancy::query()->where('link', $request->link)->first();
        if (!$vacancy || !User::query()->with('vacancies')->find(Auth::user()->id)->vacancies()->find($vacancy->id)) {
            if (!$vacancy) $vacancy = Vacancy::query()->create($request->all());
            $vacancy->users()->attach(User::query()->find(Auth::user()->id));
        }

        return response()->json(['success' => 1]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vacancies  $vacancies
     * @return \Illuminate\Http\Response
     */
    public function show(Vacancies $vacancies)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vacancies  $vacancies
     * @return \Illuminate\Http\Response
     */
    public function edit(Vacancies $vacancies)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vacancies  $vacancies
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vacancies $vacancies)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'link' => 'required'
        ]);
        $vacancy = Vacancy::query()->where('link', $request->link)->first();
        if ($vacancy && User::query()->with('vacancies')->find(Auth::user()->id)->vacancies()->find($vacancy->id)) {
            User::query()->find(Auth::user()->id)->vacancies()->detach($vacancy);
        }
        return response()->json(['success' => 1]);
    }
}
