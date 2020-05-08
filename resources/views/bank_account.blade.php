@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Bank Account</div>

                <div class="card-body">
                    <div class="container">
                        <div class="row" style="padding-bottom: 1em;">

                            <div class="form-group row">
                                <label for="balance" class="col-md-6 col-form-label text-md-right">Current Balance</label>

                                <div class="col-md-6">
                                    <input type="number" id="balance" class="form-control" min="1.00" step="1" value="{{ auth()->user()->getBankAccount()->balance }}" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="next_paycheck" class="col-md-6 col-form-label text-md-right">Next Paycheck</label>

                                <div class="col-md-6">
                                    <input class="form-control" type="date" value="@if(auth()->user()->getBankAccount()->next_paycheck == null){{date('Y-m-d')}}@else{{auth()->user()->getBankAccount()->next_paycheck}}@endif" id="next_paycheck" name="next_paycheck">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="col-form-label text-md-right" for="days-next-paycheck">Days Till Next Paycheck</label>
                                    <input type="number" id="days-next-paycheck" name="days-next-paycheck" class="form-control" min="1.00" step="1" value="{{ $days_next_paycheck }}" disabled />
                                </div>
                                <br />
                                <div class="col-md-6">
                                    <label class="col-form-label text-md-right" for="daily-budget">Total Daily Budget</label>
                                    <input type="text" id="daily-budget" name="daily-budget" class="form-control" value="${{ round($daily_budget) }}" disabled />
                                </div>
                                <br />
                                <div class="col-md-6">
                                    <label class="col-form-label text-md-right" for="monthly-budget">Budget needed till next paycheck</label>
                                    <input type="text" id="monthly-budget" name="monthly-budget" class="form-control" value="${{ $days_next_paycheck * round($daily_budget) }}" disabled />
                                </div>
                                <br />
                                <div class="col-md-6">
                                    <label class="col-form-label text-md-right" for="fun-money">Fun Money till next paycheck</label>
                                    <input type="text" id="fun-money" name="fun-money" class="form-control" value="${{ auth()->user()->getBankAccount()->balance - ($days_next_paycheck * round($daily_budget)) }}" disabled />
                                </div>
                                <br />
                                <button type="button" class="btn btn-primary" style="position: absolute; right: 10px; top: 5px;" onclick="updateMonthlyIncome()">
                                    Make Monthly Income
                                </button>
                            </div>

                            <div class="form-group row" style="width: 100%;">
                                <button type="button" class="btn btn-success" style="position: absolute; right: 10px; top: 5px;" onclick="updateBankAccount()">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

    function updateBankAccount() {
        $.ajax({
            url: "{{ route('update_bank_account') }}",
            type: 'POST',
            data: {
                balance: $('#balance').val(),
                next_paycheck: $('#next_paycheck').val(),
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                $('#days-next-paycheck').val('$' + msg['days_next_paycheck']);
                $('#daily-budget').val('$' + msg['daily_budget']);
                $('#monthly-budget').val('$' + msg['monthly_budget']);
                $('#fun-money').val('$' + msg['fun_money']);
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

    function updateMonthlyIncome() {
        $.ajax({
            url: "{{ route('update_monthly_income_bankacct') }}",
            type: 'POST',
            data: {
                balance: $('#balance').val(),
                next_paycheck: $('#next_paycheck').val(),
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                Swal.fire({
                    title: 'Success!',
                    text: msg['msg'],
                    type: 'success',
                    showCancelButton: false,
                });
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
