@extends('layout')

@section('content')
    <div class="page-header">
        <h1>Delete Game {{ $game->title }} <small>are you sure?</small></h1>
    </div>

    <form method="post" action="{{ action('GamesController@handleDelete', $game->id) }}">
        <input type="submit" class="btn btn-primary" value="Yes">
        <a href="{{ action('GamesController@edit', $game->id) }}" class="btn btn-primary">No Way!</a>
    </form>
@stop
