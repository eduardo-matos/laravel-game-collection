<?php

class LoginController extends BaseController
{
    public function index()
    {
        return View::make('login.index');
    }

    public function handleLogin()
    {
        if(Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password')])) {
            return Redirect::to('/');
        }

        return View::make('login.index', ['error' => true]);
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/login');
    }

}
