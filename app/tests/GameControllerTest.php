<?php

class GameControllerTest extends TestCase
{

	public function testMustBeLoggedInToAccessAllPages($value='')
	{
		Route::enableFilters();

		$user = new User();
		$user->email = 'e@m.co';
		$user->password = Hash::make('1');
		$user->save();

		$game = new Game();
		$game->title = 't';
		$game->publisher = 't';
		$game->completed = true;
		$game->owner = $user->id;
		$game->save();

		$this->call('GET', '/');
		$this->assertRedirectedTo('/login');
		$this->call('GET', '/create');
		$this->assertRedirectedTo('/login');
		$this->call('GET', "/edit/{$game->id}");
		$this->assertRedirectedTo('/login');
		$this->call('GET', "/delete/{$game->id}");
		$this->assertRedirectedTo('/login');
		$this->call('POST', '/create', ['title' => 't', 'publisher' => 'p', 'completed' => false]);
		$this->assertRedirectedTo('/login');
		$this->call('POST', "/edit", ['title' => 't', 'publisher' => 'p', 'completed' => true, 'id' => $game->id]);
		$this->assertRedirectedTo('/login');
		$this->call('POST', "/delete/{$game->id}");
		$this->assertRedirectedTo('/login');
	
		Auth::loginUsingId($user->id);

		$this->call('GET', '/');
		$this->assertResponseStatus(200);
		$this->call('GET', '/create');
		$this->assertResponseStatus(200);
		$this->call('GET', "/edit/{$game->id}");
		$this->assertResponseStatus(200);
		$this->call('GET', "/delete/{$game->id}");
		$this->assertResponseStatus(200);
		$this->call('POST', '/create', ['title' => 't', 'publisher' => 'p', 'completed' => false]);
		$this->assertResponseStatus(302);
		$this->call('POST', "/edit", ['title' => 't', 'publisher' => 'p', 'completed' => true, 'id' => $game->id]);
		$this->assertResponseStatus(302);
		$this->call('POST', "/delete/{$game->id}");
		$this->assertResponseStatus(302);

		Route::disableFilters();
	}

	public function testIndexPageShowsGamesOwnedOnlyByCurrentLoggedInUser()
	{
		$game1 = $this->createGame();
		$game2 = $this->createGame();

		Auth::loginUsingId($game1->owner);
		$response = $this->call('GET', '/');
		$this->assertContains($game1->title, $response->getContent());
		$this->assertNotContains($game2->title, $response->getContent());

		Auth::logout();

		Auth::loginUsingId($game2->owner);
		$response = $this->call('GET', '/');
		$this->assertContains($game2->title, $response->getContent());
		$this->assertNotContains($game1->title, $response->getContent());

	}

	public function testDeletePageExists()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'owner' => 1, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$this->client->request('GET', "/delete/{$id}");

		$this->assertResponseStatus(200);
	}

	public function testHandleDeletePageActuallyRemovesRecordFromDatabase()
	{
		$games = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'owner' => 1, 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($games);

		$this->client->request('POST', "/delete/{$id}");

		$this->assertEquals(DB::table('games')->count(), 0);
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testDeletePageReponseIs404WhenGameDoesNotExist()
	{
		$this->client->request('GET', "/delete/999");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testHandleDeletePageReponseIs404WhenGameDoesNotExist()
	{
		$this->client->request('POST', "/delete/999");
	}

	public function testHandleDeletePageRedirectsToIndexWhenAfterRemovingRecordFromDatabase()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'owner' => 1, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$crawler = $this->client->request('POST', "/delete/{$id}");

		$this->assertRedirectedTo('/');
	}

	public function testCreatePageExists()
	{
		$this->client->request('GET', '/create');
		$this->assertResponseStatus(200);
	}

	public function testHandleCreatePageActuallyCreatesGame()
	{
		$id = DB::table('users')->insertGetId(['email' => 'e@e.co', 'password' => Hash::make('a'), 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13']);
		Auth::loginUsingId($id);
		$gameInput = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false];
		$this->client->request('POST', '/create', $gameInput);
		$this->assertEquals(DB::table('games')->count(), 1);
	}

	public function testHandleCreateShowErrorsWhenGameDoesNotValidate()
	{
		$gameInput = ['title' => 't'];
		$this->client->request('POST', '/create', $gameInput);
		$this->assertContains('errors', strtolower($this->client->getResponse()->getContent()));	
	}

	public function testHandleCreateFillValidVieldsWhenThereAreErrorsInOtherFields()
	{
		$gameInput = ['title' => 't'];
		$this->client->request('POST', '/create', $gameInput);
		$this->assertContains('value="t"', strtolower($this->client->getResponse()->getContent()));	

		$gameInput = ['publisher' => 'p'];
		$this->client->request('POST', '/create', $gameInput);
		$this->assertContains('value="p"', strtolower($this->client->getResponse()->getContent()));

		$gameInput = ['completed' => true];
		$this->client->request('POST', '/create', $gameInput);
		$this->assertContains('checked="checked"', strtolower($this->client->getResponse()->getContent()));
	}

	public function testEditPageExists()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'owner' => 1, 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$this->client->request('GET', "/edit/{$id}");
		$this->assertResponseStatus(200);
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testEditPageResponseIs404WhenGameDoesNotExist()
	{
		$this->client->request('GET', "/edit/999");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testHandleEditPageResponseIs404WhenGameDoesNotExist()
	{
		$this->client->request('POST', "/edit/999");
	}

	public function testHandleEditPageActuallyUpdatesGame()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'owner' => 1, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$input = ['id' => $id, 'title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->client->request('POST', "/edit", $input);

		$game = Game::find($id);
		$this->assertEquals('Test 2', $game->title);
		$this->assertEquals('Publisher 2', $game->publisher);
		$this->assertEquals(true, $game->completed);
	}

	public function testHandleEditPageRedirectsToIndexAfterUpdatingGame()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'owner' => 1, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$input = ['id' => $id, 'title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->client->request('POST', "/edit", $input);
		
		$this->assertRedirectedTo('/');
	}

	public function testHandleEditShowErrorsWhenGameDoesNotValidate()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'owner' => 1, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$gameInput = ['completed' => false, 'id' => $id];
		$this->client->request('POST', '/edit', $gameInput);
		$this->assertContains('errors', strtolower($this->client->getResponse()->getContent()));	
	}

	public function createUserAndLogHimIn()
	{
		$user = new User();
		$user->email = 'e@m.co';
		$user->password = Hash::make('a');
		$user->save();

		Auth::logout();
		Auth::loginUsingId($user->id);

		return $user;
	}

	public function createGame()
	{
		$user = $this->createUserAndLogHimIn();
		$game = new Game();
		$game->title = md5(microtime());
		$game->publisher = md5(microtime());
		$game->completed = true;
		$game->owner = $user->id;
		$game->save();

		return $game;
	}
}