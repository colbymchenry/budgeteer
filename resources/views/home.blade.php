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
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
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
                    $.ajax({
                        url: "{{ route('add_expense') }}",
                        type: 'POST',
                        data: {
                            category: category,
                            amount: amount,
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
    </script>
@endsection
