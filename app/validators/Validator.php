<?php

namespace Validators;

abstract class AbstractValidator
{
    protected $attributes;

    public function __construct($input = null)
    {
        $this->attributes = $input? $input: \Input::all();
    }

    public function passes()
    {
        $validation = \Validator::make($this->attributes, static::$rules);

        if($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }
}
