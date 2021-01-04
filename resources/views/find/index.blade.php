@extends('layouts.app')

@section('title', 'Find')

@section('content')
    <div class="container text-dark">
        <div class="row mb-4">
            <div class="col-12">
                <input class="search_field" type="search">
            </div>
            <div class="col-12 text-right">
                <button class="btn btn-secondary" onclick="Site.show()">Sites</button>
                <button class="btn btn-secondary" onclick="Country.show()">Countries</button>
                <button class="btn btn-primary" onclick="search()">Search</button>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 vacancies_row">
        </div>
    </div>
    <!-- Modal sites -->
    <div class="modal fade" id="modal_sites" tabindex="-1" role="dialog" aria-labelledby="modal_sites_title"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_sites_title">Sites</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" name="sites" value="hh">
                            </div>
                        </div>
                        <input type="text" class="form-control" value="HeadHunter" disabled>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" name="sites" value="indeed">
                            </div>
                        </div>
                        <input type="text" class="form-control" value="Indeed (Doesn't work)" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal countries -->
    <div class="modal fade" id="modal_countries" tabindex="-1" role="dialog" aria-labelledby="modal_countries_title"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_countries_title">Countries</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush country text-dark">
                    </ul>
                    <div class="input-group mt-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">Country</span>
                        </div>
                        <input type="text" class="form-control" id="country_add_field" aria-label="Default"
                               aria-describedby="inputGroup-sizing-default">
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-success" onclick="Country.add()">Add</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let countries = ['Russia'];
        let sites = [];

        document.addEventListener("DOMContentLoaded", function (event) {
            Site.options();
        });

        class Site {
            static options() {
                $('#modal_sites').find('input[type="checkbox"]').change(function () {
                    if (this.checked) {
                        sites.push(this.value)
                    } else {
                        let index = sites.indexOf(this.value);
                        if (index > -1) {
                            sites.splice(index, 1);
                        }
                    }
                });
            }

            static show() {
                let modal = $('#modal_sites');
                modal.modal('show');
            }
        }

        class Country {
            static show() {
                let modal = $('#modal_countries');
                Country.fill(modal);
                modal.modal('show');
            }

            static add() {
                let country = $('#country_add_field').val();
                let index = countries.indexOf(country);

                if (index > -1) {
                    alert('Such country already exists');
                } else {
                    countries.push(country);
                    Country.fill($('#modal_countries'));
                }
            }

            static delete(button) {
                button = $(button);
                let country = button.parent().find('span').text();
                let index = countries.indexOf(country);

                if (index > -1) {
                    countries.splice(index, 1);
                    Country.fill($('#modal_countries'));
                }
            }

            static fill(modal) {
                let list = modal.find('.list-group.list-group-flush.country');
                list.html('');
                if (countries) {
                    countries.forEach(country => {
                        list.append(`<li class="list-group-item"><span>${country}</span><button class="country_delete" onclick="Country.delete(this)">&times;</button></li>`);
                    })
                }
            }
        }

        class Vacancy {
            static create(json) {
                try {
                    let vacancies = json;
                    let vacanciesRow = $('.vacancies_row');
                    vacanciesRow.html('');
                    vacancies['response'].forEach(vacancy => {
                        vacanciesRow.append(`
                                    <div class="col mb-4">
                                        <div class="card card-work">
                                            <button class="favourite" data-favourite=${vacancy['isFavourite'] ? 1 : 0} onclick="Vacancy.action(this)">${vacancy['isFavourite'] ? '&#9733' : '☆' }</button>
                                            <div class="card-body">
                                                <h4 class="card-title vacancy_title">${vacancy['title']}</h4>
                                                <h5 class="card-title vacancy_city">${vacancy['city']}</h5>
                                                <h6 class="card-title vacancy_salary">${vacancy['salary'] ? vacancy['salary'] : ''}</h6>
                                                <h6 class="card-title vacancy_company">${vacancy['company'] ? vacancy['company'] : ''}</h6>
                                                <h6 class="card-title vacancy_site">hh</h6>
                                                <p class="card-text vacancy_description">${vacancy['description']}</p>
                                            </div>
                                        </div>
                                        <a href="${vacancy['link']}" target="_blank" class="btn btn-primary vacancy_link">Go to vacancy</a>
                                    </div>
                        `);
                    })
                } catch (ex) {
                    console.log(json)
                    alert(ex)
                }
            }

            static action(btn) {
                btn = $(btn);
                if (btn.data('favourite') == '1') {
                    Vacancy.delete(btn);
                } else {
                    Vacancy.save(btn);
                }
            }

            static delete(btn) {
                let parent = btn.parent().parent();
                $.ajax({
                    url: '{{route('profile.vacancy.destroy')}}',
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{csrf_token()}}',
                        title: parent.find('.vacancy_title').text(),
                        location: parent.find('.vacancy_city').text(),
                        salary: parent.find('.vacancy_salary').text(),
                        company: parent.find('.vacancy_company').text(),
                        description: parent.find('.vacancy_description').text(),
                        link: parent.find('.vacancy_link').attr('href'),
                        site: parent.find('.vacancy_site').text()
                    },
                    success: function (response) {
                        btn.html('☆')
                        btn.data('favourite', '0');
                    }
                })
            }

            static save(btn) {
                let parent = btn.parent().parent();
                console.log(parent.find('.vacancy_link').attr('href'))
                $.ajax({
                    url: '{{route('profile.vacancy.store')}}',
                    type: 'POST',
                    data: {
                        _method: 'POST',
                        _token: '{{csrf_token()}}',
                        title: parent.find('.vacancy_title').text(),
                        location: parent.find('.vacancy_city').text(),
                        salary: parent.find('.vacancy_salary').text(),
                        company: parent.find('.vacancy_company').text(),
                        description: parent.find('.vacancy_description').text(),
                        link: parent.find('.vacancy_link').attr('href'),
                        site: parent.find('.vacancy_site').text()
                    },
                    success: function (response) {
                        btn.html('&#9733;')
                        btn.data('favourite', '1');
                    }
                })
            }
        }

        function search() {
            $.ajax({
                url: '{{route('find.search')}}',
                type: 'POST',
                data: {
                    _method: 'POST',
                    _token: '{{csrf_token()}}',

                    options: {
                        sites,
                        countries,
                        query: $('.search_field').eq(0).val(),
                    }
                },
                success: function (response) {
                    Vacancy.create(response)
                }
            })
        }


    </script>
@show
