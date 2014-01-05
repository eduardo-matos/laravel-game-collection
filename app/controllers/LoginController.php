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

        return View::make('login.index')->withErrors(['email/password' => 'email and password don\'t match']);
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/login');
    }

    public function signup()
    {
        return View::make('login.signup');
    }

    public function handleSignup()
    {
        $validator = new Validators\User(Input::all());
        if($validator->passes()) {
            $user = new User;
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save();

            return Redirect::to('/login');
        }

        return View::make('login.signup')->with(Input::all())->withErrors($validator->errors);
    }

}
