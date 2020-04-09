@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Expenses for {{ $category->name }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col" style="width: 10%">Date</th>
                            <th scope="col" style="width:30%">Amount</th>
                            <th scope="col" style="width:60%">Memo</th>
                            <th scope="col"></th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                                <tr id="expense-{{ $expense->id }}">
                                <th scope="row" ><small>{{ $expense->created_at->format('m/d/Y') }}</small></th>
                                    <td><small>${{ $expense->amount }}</small></td>
                                    <td><small>{{ ($expense->memo == '' ? 'N/A' : $expense->memo) }}</small></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="delExpense('{{ $expense->id }}')" style="display: block;margin: auto;"><i class="fa fa-close"></i></button>
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
        function delExpense(id) {
            $.ajax({
                url: "{{ route('del_expense') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
            }).done(function (msg) {
                if (msg['success']) {
                    $('#expense-' + id).remove();
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
