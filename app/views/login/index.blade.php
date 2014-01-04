@extends('layout');

@section('content')

    @if(isset($error) and $error)
        <div class="error">
            User not found!
        </div>
    @endif;

    <form action="{{ action('LoginController@handleLogin') }}" method="POST">

        <div class="form-group">
            <label for="email">email</label>
            <input class="form-control" name="email" id="email" value="{{ Input::get('email', '') }}">
        </div>

        <div class="form-group">
            <label for="password">password</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>

        <input type="submit" value="Save" class="btn btn-primary">
    </form>
@stop
