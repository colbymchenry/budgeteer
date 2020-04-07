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
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                            <div class="row">
                                <tr>
                                <th scope="row" ><small>{{ $expense->created_at->format('m/d/Y') }}</small></th>
                                    <td><small>${{ $expense->amount }}</small></td>
                                    <td><small>{{ ($expense->memo == '' ? 'N/A' : $expense->memo) }}</small></td>
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

    </script>
@endsection
