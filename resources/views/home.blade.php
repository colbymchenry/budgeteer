@extends('layouts.app')

@section('content')
<div class="container">
    {{-- <div class="row justify-content-center"> --}}
        {{-- <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(auth()->user()->getBankAccount()->next_paycheck != null && auth()->user()->getBankAccount()->next_paycheck < now())
                        <div class="alert alert-warning" role="alert">
                            Update Next Paycheck in <a href="{{ route('bank_account') }}">Bank Account</a>
                        </div>
                    @endif
                    <button onclick="addExpense()" class="btn btn-secondary" style="display: block;margin: auto;width: 10em;height: 10em;"><span style="font-size: 84px;">+</span></button>
                </div>
            </div>
        </div> --}}
    {{-- </div> --}}
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

                    <div class="row">
                        <div class="col-8">
                            <small>Monthly Income:</small>
                        </div>
                        <div class="col">
                            <small>&nbsp;&nbsp;&nbsp;${{ auth()->user()->monthly_income }}</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <small>Budgeted Expenses:</small>
                        </div>
                        <div class="col">
                            <small>+ ${{ \App\Category::where('user', auth()->user()->id)->sum('limit') }}</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <small>Actual Expenses:</small>
                        </div>
                        <div class="col">
                            <small id="actual-expenses">- ${{ $actual_expenses }}</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <small>Left for Budget:</small>
                        </div>
                        <div class="col">
                            <small id="left-for-budget">= ${{ ($left_for_budget < 0 ? 0 : $left_for_budget) }}</small>
                        </div>
                    </div>

                    <hr />
                    <div class="row">
                        <div class="col-8">
                            @if($left_for_budget < 0)
                                <small id="fun-money-label">Budgeted Fun Money: (${{ auth()->user()->monthly_income }} - ${{ $actual_expenses }})</small>
                            @else
                                <small id="fun-money-label">Budgeted Fun Money: (${{ auth()->user()->monthly_income }} - ${{ \App\Category::where('user', auth()->user()->id)->sum('limit') }})</small>
                            @endif
                        </div>
                        <div class="col">
                            <small id="fun-money">&nbsp;&nbsp;&nbsp;${{ $fun_money->limit }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <small>Fun Money Spent:</small>
                        </div>
                        <div class="col">
                            <small id="left-for-budget">- ${{ $fun_money_spent }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <small>Fun Money Left:</small>
                        </div>
                        <div class="col">
                            <small id="left-for-budget">= ${{ ($fun_money->limit - $fun_money_spent) }}</small>
                        </div>
                    </div>

                    <hr />
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
                            <tbody id="expenses_body">
                                <tr>
                                    <th scope="row" >
                                        <a id="category-expenses_{{ $fun_money->id }}" href="{{ route('view_expenses') }}?category={{ $fun_money->id}}&month={{ $month }}"><u>{{ $fun_money->name }}</u></a>
                                    </th>
                                    <td>
                                        <div class="progress" onclick="addExpense('{{ $fun_money->id }}', '{{ $fun_money->name }}')">
                                            <div class="progress-bar @if(round(($fun_money->getTotalForMonth($month) / $fun_money->limit) * 100) >= 100) bg-danger @endif" role="progressbar" style="width: {{ round(($fun_money->getTotalForMonth($month) / $fun_money->limit) * 100) }}%;" aria-valuenow="{{ round(($fun_money->getTotalForMonth($month) / $fun_money->limit) * 100) }}" aria-valuemin="0" aria-valuemax="100" id="progress_{{ $fun_money->id }}">{{ round(($fun_money->getTotalForMonth($month) / $fun_money->limit) * 100) }}%</div>
                                        </div>
                                    </td>
                                </tr>
                                @foreach(\App\Category::where('user', auth()->user()->id)->get() as $category)
                                @if(!$category->recurring)
                                    <tr>
                                        <th scope="row" >
                                            <a id="category-expenses_{{ $category->id }}" href="{{ route('view_expenses') }}?category={{ $category->id}}&month={{ $month }}"><u>{{ $category->name }}</u></a>
                                        </th>
                                        <td>
                                            <div class="progress" onclick="addExpense('{{ $category->id }}', '{{ $category->name }}')">
                                                <div class="progress-bar @if(round(($category->getTotalForMonth($month) / $category->limit) * 100) >= 100) bg-danger @endif" role="progressbar" style="width: {{ round(($category->getTotalForMonth($month) / $category->limit) * 100) }}%;" aria-valuenow="{{ round(($category->getTotalForMonth($month) / $category->limit) * 100) }}" aria-valuemin="0" aria-valuemax="100" id="progress_{{ $category->id }}">{{ round(($category->getTotalForMonth($month) / $category->limit) * 100) }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
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
        var monthly_income = "{{ auth()->user()->monthly_income }}";
        var budgeted_expenses = "{{ \App\Category::where('user', auth()->user()->id)->sum('limit') }}";

        $(document).ready(function() {
            $('#selection-month').on('change', function() {
                var selected_month = $('#selection-month').val();
                var id = this.id.split('-')[1];

                window.location.href = `/home?month=${selected_month}`;
            });
        });

        var regex  = /^[1-9]\d*(((,\d{3}){1})?(\.\d{0,2})?)$/;

        var categories_json = {!! json_encode(\App\Category::where('user', auth()->user()->id)->get()); !!};
        var categories_array = [];
        categories_json.forEach(obj => {
            categories_array[obj['id']] = obj['name'];
        });

        categories_array[-1] = "Fun Money";


        async function addExpense(category_id, name) {

            const { value: amount } = await Swal.fire({
                title: 'Amount',
                input: 'number',
                showCancelButton: true,
                confirmButtonText: 'Next',
                inputAttributes: {
                  pattern: '\\d*'
                },
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
                
                
                if(memo) {
                    $.ajax({
                        url: "{{ route('add_expense') }}",
                        type: 'POST',
                        data: {
                            category: category_id,
                            amount: amount,
                            memo: memo,
                            month: $('#selection-month').val(),
                            _token: '{{ csrf_token() }}'
                        },
                    }).done(function (msg) {
                        if (msg['success']) {
                            var percentage = Math.round(parseFloat(msg['percentage']));
                            var progress_bar = $(`#progress_${category_id}`);
                            progress_bar.attr('aria-valuenow', percentage).width(`${percentage}%`);
                            progress_bar.text(percentage + "%");
                            if(percentage >= 100) {
                                progress_bar.addClass('bg-danger');
                            } else {
                                progress_bar.removeClass('bg-danger');
                            }
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
