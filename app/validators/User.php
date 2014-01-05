<?php

namespace Validators;

class User extends AbstractValidator
{
    protected static $rules = array(
        'email' => 'required|email|unique:users',
        'password' => 'required',
    );
}
