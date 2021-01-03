@extends('layouts.app')

@section('title', 'profile')

@section('content')
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 vacancies_row text-dark">
            @foreach($vacancies as $vacancy)
                <div class="col mb-4">
                    <div class="card card-work">
                        <div class="card-body">
                            <h4 class="card-title vacancy_title">{{$vacancy->title}}</h4>
                            <h5 class="card-title vacancy_city">{{$vacancy->location}}</h5>
                            <h6 class="card-title vacancy_salary">{{$vacancy->salary}}</h6>
                            <h6 class="card-title vacancy_company">{{$vacancy->company}}</h6>
                            <h6 class="card-title vacancy_site">{{$vacancy->site}}</h6>
                            <p class="card-text vacancy_description">{{$vacancy->description}}</p>
                        </div>
                        <a href="{{$vacancy->link}}" class="btn btn-primary vacancy_link">Go to vacancy</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
