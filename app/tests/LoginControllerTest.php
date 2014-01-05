<?php

class LoginControllerTest extends TestCase
{

	public function testLoginPageExists()
	{
		$this->call('GET', '/login');
		$this->assertResponseStatus(200);
	}

	public function testLogoutPageExists()
	{
		$this->call('GET', '/logout');
		// redirects to /login anyway
		$this->assertResponseStatus(302);
	}

	public function testSignupPageExists()
	{
		$this->call('GET', '/signup');
		$this->assertResponseStatus(200);
	}

	public function testLoginPageActuallyLoginUser()
	{
		$user = new User();
		$user->email = 'e@m.co';
		$user->password = Hash::make('1');
		$user->save();

		$this->assertTrue(Auth::guest());
		$this->call('POST', '/login', ['email' => $user->email, 'password' => '1']);
		$this->assertFalse(Auth::guest());
	}

	public function testAfterLoginUserIsRedirectedToHome()
	{
		$user = new User();
		$user->email = 'e@m.co';
		$user->password = Hash::make('1');
		$user->save();

		$this->call('POST', '/login', ['email' => $user->email, 'password' => '1']);
		$this->assertRedirectedTo('/');
	}

	public function testShowErroMessageWhenUserIsNotAbleToLogin()
	{
		$response = $this->call('POST', '/login', ['email' => 'a@a.com', 'password' => '1']);
		$this->assertContains('error', strtolower($response->getContent()));
	}

	public function testAutoFillUsernameIfLoginIsNotSuccessful()
	{
		$response = $this->call('POST', '/login', ['email' => 'a@a.com', 'password' => '1']);
		$this->assertContains('value="a@a.com"', strtolower($response->getContent()));
	}

	public function testLogoutUserWhenAccessLogoutPage()
	{
		$user = new User();
		$user->email = 'e@m.co';
		$user->password = Hash::make('1');
		$user->save();
		Auth::loginUsingId($user->id);

		$this->call('GET', '/logout');
		$this->assertTrue(Auth::guest());
	}

	public function testUserMustBeRedirectedToLoginAfterLogout()
	{

		$user = new User();
		$user->email = 'e@m.co';
		$user->password = Hash::make('1');
		$user->save();
		Auth::loginUsingId($user->id);

		$this->call('GET', '/logout');
		$this->assertRedirectedTo('/login');
	}

	public function testSignupPageActuallyCreateUser()
	{
		$data = ['email' => 'a@a.com', 'password' => 'a'];
		$this->call('POST', '/signup', $data);

		$this->assertEquals(User::all()->count(), 1);
	}

	public function testSignupPageRedirectsToLoginAfterCreatingUser()
	{
		$data = ['email' => 'a@a.com', 'password' => 'a'];
		$this->call('POST', '/signup', $data);

		$this->assertRedirectedTo('/login');
	}

	public function testSignupPageShowsErrorWhenTryingToCreateUserWithAlreadyExistingEmail()
	{
		$user = new User;
		$user->email = 'a@a.com';
		$user->password = Hash::make('a');
		$user->save();

		$data = ['email' => 'a@a.com', 'password' => 'b'];
		$response = $this->call('POST', '/signup', $data);

		$this->assertContains('error', $response->getContent());
	}
}
