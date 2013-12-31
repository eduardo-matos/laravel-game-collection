@extends('layout')

@section('content')
    <div class="page-header">
        <h1>Create Game <small>and someday finish it!</small></h1>
    </div>

    <form action="{{ action('GamesController@handleCreate') }}" method="post">
        <div class="form-group">
            <label for="title">Title</label>
            <input class="form-control" name="title" id="title">
        </div>

        <div class="form-group">
            <label for="publisher">Publisher</label>
            <input class="form-control" name="publisher" id="publisher">
        </div>

        <div class="checkbox">
            <label for="complete">
                <input type="checkbox" name="complete" id="complete"> Complete?
            </label>
        </div>

        <input type="submit" value="Create" class="btn btn-primary">
        <a href="{{ action('GamesController@index') }}" class="btn btn-primary">Cancel</a>
    </form>
@stop
