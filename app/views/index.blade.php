@extends('layout')

@section('content')
    <div class="page-header">
        <h1>All Games <small>Gotta catch 'em all!</small></h1>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <a href="{{ action('GamesController@create') }}" class="btn btn-primary">Create Game</a>
        </div>
    </div>

    @if($games->isEmpty())
        <p>There are no games!</p>
    @else
        <table class="table table-striped">
            <thead>
                <th>Title</th>
                <th>Publisher</th>
                <th>Complete</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($games as $game)
                    <tr>
                        <td>{{ $game->title }}</td>
                        <td>{{ $game->publisher }}</td>
                        <td>{{ $game->completed? 'Yes': 'No' }}</td>
                        <td>
                            <a href="{{ action('GamesController@edit', $game->id) }}">Edit</a>
                            <a href="{{ action('GamesController@delete', $game->id) }}">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@stop
