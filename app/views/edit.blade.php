@extends('layout')

@section('content')
    <div class="page-header">
        <h1>Edit Game <small>Go on, mark it complete!</small></h1>
    </div>

    @include('_partials.errors')

    <form action="{{ action('GamesController@handleEdit', [$game->id]) }}" method="post">

        <div class="form-group">
            <label for="title">Title</label>
            <input class="form-control" name="title" id="title" value="{{ $game->title }}">
        </div>

        <div class="form-group">
            <label for="publisher">Publisher</label>
            <input class="form-control" name="publisher" id="publisher" value="{{ $game->publisher }}">
        </div>

        <div class="checkbox">
            <label for="completed">
                <input type="checkbox" name="completed" id="completed" {{ $game->completed? 'checked': '' }} > Completed?
            </label>
        </div>

        <input type="submit" value="Save" class="btn btn-primary">
        <a href="{{ action('GamesController@index') }}" class="btn btn-primary">Cancel</a>
    </form>
@stop
