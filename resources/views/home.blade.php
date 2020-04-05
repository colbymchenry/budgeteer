@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <button onclick="addExpense()" class="btn btn-secondary" style="display: block;margin: auto;width: 10em;height: 10em;"><span style="font-size: 84px;">+</span></button>
                </div>
            </div>
        </div>
    </div>
    <p></p>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <select id="selection-month" name="select-month">
                @foreach($month_selections_html as $html)
                    {!! $html !!}
                @endforeach
            </select>

            {{-- TODO: Make red if over 100% or get week number and make sure if in first week under 25% second under 50% third under 75% fourth under 100% and change to red if over for week --}}
            <div class="card">
                <div class="card-header">Current Month ({{ date('F Y') }})</div>

                <div class="card-body">
                        <small>Tip! Week 1 stay under 25%, Week 2 stay under 50%</small>
                        <br />
                        <small>Tip! Week 3 stay under 75%, Week 4 stay under 100%</small>
                        <p> </p>
                        <table class="table">
                            <thead>
                              <tr>
                                <th scope="col" style="width: 40%">Category</th>
                                <th scope="col" style="width:60%">Monthly Usage</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Category::where('user', auth()->user()->id)->get() as $category)
                                <div class="row">
                                    <tr>
                                        <th scope="row" >
                                            <a id="category-expenses-{{ $category->id }}" href="{{ route('view_expenses') }}?category={{ $category->id}}&month=4"><u>{{ $category->name }}</u></a>
                                        </th>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar @if(round(($category->getTotalForMonth(4) / $category->limit) * 100) >= 100) bg-danger @endif" role="progressbar" style="width: {{ round(($category->getTotalForMonth(4) / $category->limit) * 100) }}%;" aria-valuenow="{{ round(($category->getTotalForMonth(4) / $category->limit) * 100) }}" aria-valuemin="0" aria-valuemax="100" id="progress-{{ $category->id }}">{{ round(($category->getTotalForMonth(4) / $category->limit) * 100) }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                </div>
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
        var expenses_link = "{{ route('view_expenses') }}";

        $(document).ready(function() {
            $('#selection-month').on('change', function() {
                var selected_month = $('#selection-month').val();
                var id = this.id.split('-')[1];
                $('[id^="category-expenses-"]').each(function() {
                    var category_id = this.id.split('-')[2];
                    $(this).attr('href', expenses_link + `?category=${category_id}&month=${selected_month}`);
                });
            });
        });

        var regex  = /^[1-9]\d*(((,\d{3}){1})?(\.\d{0,2})?)$/;

        var categories_json = {!! json_encode(\App\Category::where('user', auth()->user()->id)->get()); !!};
        var categories_array = [];
        categories_json.forEach(obj => {
            categories_array[obj['id']] = obj['name'];
        });

        categories_array[-1] = "Uncategorized";


        async function addExpense() {

            const { value: category } = await Swal.fire({
                title: 'Which Category:',
                input: 'select',
                inputOptions: categories_array,
                inputPlaceholder: 'Select a Category',
                showCancelButton: true,
                confirmButtonText: 'Next',
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        resolve();
                    });
                }
            });

            if (category) {
                const { value: amount } = await Swal.fire({
                    title: 'Amount',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Next',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to write something!'
                        }

                        if(!regex.test(value)) {
                            return 'Invalid amount';
                        }
                    }
                });

                if(amount) {

                    const { value: memo } = await Swal.fire({
                        title: 'Memo',
                        text: 'Not required',
                        input: 'text',
                        showCancelButton: true
                    });

                    $.ajax({
                        url: "{{ route('add_expense') }}",
                        type: 'POST',
                        data: {
                            category: category,
                            amount: amount,
                            memo: memo,
                            _token: '{{ csrf_token() }}'
                        },
                    }).done(function (msg) {
                        if (msg['success']) {

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
        }

        function viewExpenses(category_id) {
            $.ajax({
                url: "{{ route('view_expenses') }}",
                type: 'GET',
                data: {
                    category: category_id,
                    month: $('#selection-month').val(),
                    _token: '{{ csrf_token() }}'
                },
            }).done(function (msg) {
                if (msg['success']) {

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
