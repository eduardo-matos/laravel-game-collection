@extends('layout')

@section('content')
    <div class="page-header">
        <h1>Create Game <small>and someday finish it!</small></h1>
    </div>

    @include('_partials.errors')

    <form action="{{ action('GamesController@handleCreate') }}" method="post">
        <div class="form-group">
            <label for="title">Title</label>
            {{ Form::text('title', isset($title)? $title:'', ['class' => 'form-control']) }}
        </div>

        <div class="form-group">
            <label for="publisher">Publisher</label>
            {{ Form::text('publisher', isset($publisher)? $publisher:'', ['class' => 'form-control']) }}
        </div>

        <div class="checkbox">
            <label for="complete">
                {{ Form::checkbox('completed', '1', isset($completed)? $completed:'') }}
                Completed?
            </label>
        </div>

        <input type="submit" value="Create" class="btn btn-primary">
        <a href="{{ action('GamesController@index') }}" class="btn btn-primary">Cancel</a>
    </form>
@stop
