@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Budget Settings</div>

                <div class="card-body">
                    <div class="container">
                        <div class="row" style="padding-bottom: 1em;">

                            <div class="form-group row">
                                <label for="income-monthly" class="col-md-6 col-form-label text-md-right">Monthly Income</label>
                                <small style="padding-left: 1em;">* (Take out State and Federal Taxes)</small>

                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <input type="number" id="income-monthly" class="form-control" min="1.00" step="1" value="{{ auth()->user()->monthly_income }}" />
                                        <span id="income-monthly-error" class="invalid-feedback hidden" role="alert"></span>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="updateMonthlyIncome()" style="display: block;margin: auto;"><i class="fa fa-save"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="expenses-monthly" class="col-md-6 col-form-label text-md-right">Monthly Total Budget</label>

                                <div class="col-md-6">
                                    <input id="expenses-monthly" type="number" class="form-control" name="expenses-monthly" value="0" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="funmoney-monthly" class="col-md-6 col-form-label text-md-right">Monthly Fun Money</label>

                                <div class="col-md-6">
                                    <input id="funmoney-monthly" type="text" class="form-control" name="funmoney-monthly" value="0" disabled>
                                </div>
                            </div>

                        </div>
                        <div class="row" style="padding-bottom: 1em;">
                            <div class="col text-center">
                                <button class="btn btn-primary" onclick="addCategory()">Add Category</button>
                            </div>
                            <div class="col text-center">
                                <button class="btn btn-success" onclick="updateLimits()">Save Limits</button>
                            </div>
                        </div>
                    </div>
                    <small>Tip! Click a category name to rename it.</small>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 50%;">Name</th>
                                <th scope="col" style="width: 50%;">Monthly Limit</th>
                            </tr>
                          </thead>
                        <tbody id="categories">

                            @foreach(\App\Category::where('user', auth()->user()->id)->get() as $category)
                            <tr id="cat-{{ $category->id }}">
                                <td scope="row" style="vertical-align: middle;"><a href="#" onclick="renameCategory('{{ $category->id }}');"><u id="catname-{{ $category->id }}">{{ $category->name }}</u></a></td>
                                <td style="vertical-align: middle;">

                                    <div class="input-group mb-3">
                                        <input type="number" id="catlimit-{{ $category->id }}" class="form-control" min="1.00" step="1" value="{{ $category->limit }}" />
                                        <span id="catlimiterror-{{ $category->id }}" class="invalid-feedback hidden" role="alert"></span>
                                        <div class="input-group-append">
                                            <button class="btn btn-danger" onclick="delCategory('{{ $category->id }}')" style="display: block;margin: auto;"><i class="fa fa-close"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var categories_array = [];
    var category_limits = [];
    var categories_json = {!! json_encode(\App\Category::where('user', auth()->user()->id)->get()); !!};

    categories_json.forEach(obj => {
        categories_array[obj['id']] = obj['name'];
        category_limits[obj['id']] = obj['limit'];
    });

    categories_array[-1] = "Uncategorized";

    $(document).ready(function() {
        updateMonthlyTotal();

        // empty Invalid Amount message from limit boxs
        $('[id^="catlimit-"]').keyup(function() {
            var id = this.id.split('-')[1];
            $(`[id^="catlimiterror-${id}"]`).empty();
            $(`[id^="catlimiterror-${id}"]`).addClass('hidden');
            $(`[id^="catlimit-${id}"]`).removeClass('is-invalid');
        });
    });



    async function addCategory() {


        const { value: name } = await Swal.fire({
            inputPlaceholder: 'Name of Category',
            input: 'text',
            showCancelButton: true,
            confirmButtonText: 'Next',
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    resolve();
                });
            }
        });

        if(!name) {
            return;
        }

        const { value: limit } = await Swal.fire({
            title: 'Monthly Limit:',
            input: 'number',
            showCancelButton: true,
            confirmButtonText: 'Finish',
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    resolve();
                });
            }
        });

        if(!limit) {
            return;
        }


        $.ajax({
            url: "{{ route('add_category') }}",
            type: 'POST',
            data: {
                name: name,
                limit: limit,
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                var id = msg['id'];

                var html = `
                    <tr id="cat-${id}">
                        <td scope="row" style="vertical-align: middle;"><a href="#" onclick="renameCategory('${id}');"><u id="catname-${id}">${name}</u></a></td>
                        <td style="vertical-align: middle;">
                            <div class="input-group mb-3">
                                <input type="number" id="catlimit-${id}" class="form-control" min="1.00" step="1" value="${limit}" />
                                <span id="catlimiterror-${id}" class="invalid-feedback hidden" role="alert"></span>
                                <div class="input-group-append">
                                    <button class="btn btn-danger" onclick="delCategory('${id}')" style="display: block;margin: auto;"><i class="fa fa-close"></i></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;

                $('#categories').append(html);

                categories_array[id] = name;
                category_limits[id] = limit;
                updateMonthlyTotal();
            } else {
                Swal.fire({
                    title: 'Oops!',
                    text: msg['msg'],
                    type: 'warning',
                    showCancelButton: false,
                });
            }
        });
    }

    async function delCategory(id) {

        var refined_categories = categories_array;

        delete refined_categories[id];

        const { value: new_category } = await Swal.fire({
            title: 'All expenses in this Category will be moved to:',
            input: 'select',
            inputOptions: refined_categories,
            inputPlaceholder: 'Select a Category',
            showCancelButton: true,
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    resolve();
                });
            }
        });

        if (new_category) {
            $.ajax({
                url: "{{ route('del_category') }}",
                type: 'POST',
                data: {
                    id: id,
                    new_category: new_category,
                    _token: '{{ csrf_token() }}'
                },
            }).done(function (msg) {
                if (msg['success']) {
                    delete categories_array[id];
                    $('#cat-' + id).remove();
                    updateMonthlyTotal();
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: msg['msg'],
                        type: 'warning',
                        showCancelButton: false,
                    });
                }
            });
        }
    }

    function renameCategory(id) {
        Swal.fire({
            title: 'New name of Category:',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'on'
            },
            showCancelButton: true,
            confirmButtonText: 'Rename',
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url: "{{ route('rename_category') }}",
                    type: 'POST',
                    data: {
                        category_id: id,
                        name: result.value,
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {
                    if (msg['success']) {
                        $(`#catname-${id}`).text(result.value);

                        categories_array[id] = result.value;
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: msg['msg'],
                            type: 'warning',
                            showCancelButton: false,
                        });
                    }
                });
            }
        });
    }

    function updateLimits() {
        var regex  = /^[1-9]\d*(((,\d{3}){1})?(\.\d{0,2})?)$/;
        var valid_limits = true;

        $('[id^="catlimit-"]').each(function() {
            var category_id = this.id.split('-')[1];
            var limit = this.value;

            if(!regex.test(limit) && $(`#catlimiterror-${category_id}`).html().length < 1) {
                var html = `
                <strong>Invalid amount.</strong>
                `;
                $(`#catlimiterror-${category_id}`).removeClass('hidden');
                $(`#catlimit-${category_id}`).addClass('is-invalid');
                $(`#catlimiterror-${category_id}`).show();
                $(`#catlimiterror-${category_id}`).append(html);
                valid_limits = false;
            }
        });

        // don't continue if any of the limits are invalid
        if(!valid_limits) return;

        Swal.fire({
            title: 'Success!',
            type: 'success',
            showCancelButton: false,
        });

        $('[id^="catlimit-"]').each(function() {
            var category_id = this.id.split('-')[1];
            var limit = this.value;

            $.ajax({
                url: "{{ route('update_limits') }}",
                type: 'POST',
                data: {
                    category_id: category_id,
                    limit: limit,
                    _token: '{{ csrf_token() }}'
                },
            }).done(function (msg) {
                if (msg['success']) {
                    category_limits[category_id] = limit;
                    updateMonthlyTotal();
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: msg['msg'],
                        type: 'warning',
                        showCancelButton: false,
                    });
                }
            });
        });
    }

    function updateMonthlyTotal() {
        var total = 0;
        for (var key in category_limits) {
            total += parseFloat(category_limits[key]);
        }

        $('#expenses-monthly').val(total);

        var funmoney = parseFloat($("#income-monthly").val()) - total;
        if(funmoney == NaN) {

        }
        $('#funmoney-monthly').val(funmoney);
    }

    function updateMonthlyIncome() {
        $.ajax({
            url: "{{ route('update_monthly_income') }}",
            type: 'POST',
            data: {
                amount: $('#income-monthly').val(),
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                Swal.fire({
                    title: 'Success!',
                    type: 'success',
                    showCancelButton: false,
                });
                updateMonthlyTotal();
            } else {
                Swal.fire({
                    title: 'Oops!',
                    text: msg['msg'],
                    type: 'warning',
                    showCancelButton: false,
                });
            }
        });
    }

</script>
@endsection
