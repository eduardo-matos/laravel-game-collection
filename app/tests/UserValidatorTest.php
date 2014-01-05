<?php

use Validators\User as UserValidator;

class UserValidatorTest extends TestCase
{

	public function testEmailAndPasswordAreRequired()
	{
		$input1 = ['email' => 'a@a.com'];
		$input2 = ['password' => 'a'];

		$validator1 = new UserValidator($input1);
		$validator2 = new UserValidator($input2);

		$validator1->passes();
		$validator2->passes();

		$this->assertEquals(1, count($validator1->errors));
		$this->assertEquals(1, count($validator2->errors));
	}

	public function testEmailMustValidEmail()
	{
		$input = ['email' => 'a', 'password' => 'a'];
		$validator = new UserValidator($input);
		$validator->passes();
		$this->assertEquals(1, count($validator->errors));
	}

	public function testEmailMustBeUnique()
	{
		$user = new User;
		$user->email = 'a@a.com';
		$user->password = Hash::make('a');
		$user->save();

		$input = ['email' => 'a@a.com', 'password' => 'b'];
		$validator = new UserValidator($input);
		$validator->passes();
		$this->assertEquals(1, count($validator->errors));
	}

}
