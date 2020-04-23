@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Change Password</div>

                <div class="card-body">
                    <div class="container">
                        <div class="row" style="padding-bottom: 1em;">
                            <form action="/update_password" method="POST">
                                @csrf
                                <label for="new_password" class="col-form-label text-md-right">New Password</label>
                                <input type="password" id="new_password" class="form-control" name="new_password" />
                                <br />
                                <button class="btn btn-primary" type="submit">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
