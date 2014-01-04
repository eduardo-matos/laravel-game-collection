<?php

class GamesController extends BaseController
{
    public function index()
    {
        $games = Game::where('owner', '=', Auth::user()->id)->get();
        return View::make('index', compact('games'));
    }

    public function create()
    {
        return View::make('create');
    }

    public function handleCreate()
    {
        $validator = new Validators\Game;

        if($validator->passes()) {
            $game = new Game;
            $game->title = Input::get('title');
            $game->publisher = Input::get('publisher');
            $game->completed = Input::has('completed');
            $game->owner = Auth::user()->id;
            $game->save();

            return Redirect::action('GamesController@index');
        }

        return View::make('create')->with(Input::all())->withErrors($validator->errors);
    }

    public function edit(Game $game)
    {
        return View::make('edit', compact('game'));
    }

    public function handleEdit()
    {
        $validator = new Validators\Game;

        $game = Game::findOrFail(Input::get('id'));

        if(!$validator->passes()) {
            return View::make('edit', compact('game'))->withErrors($validator->errors);
        }

        $game->title = Input::get('title');
        $game->publisher = Input::get('publisher');
        $game->completed = Input::has('completed');
        $game->save();

        return Redirect::to('/');
    }

    public function delete(Game $game)
    {
        return View::make('delete', compact('game'));
    }

    public function handleDelete(Game $game)
    {
        $game->delete();

        return Redirect::action('GamesController@index');
    }
}
