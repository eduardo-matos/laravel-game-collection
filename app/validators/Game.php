<?php

namespace Validators;

class Game extends AbstractValidator
{
    protected static $rules = array(
        'title' => 'required',
        'publisher' => 'required',
        'completed' => 'required',
    );
}
