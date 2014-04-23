<?php

use Faker\Factory as FakerFactory;

class GameControllerTest extends TestCase
{

	public function testMustBeLoggedInToAccessAllPages($value='')
	{
		Route::enableFilters();

		$game = $this->createGame();

		Auth::logout($game->owner);

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
		$this->call('POST', "/edit/{$game->id}", ['title' => 't', 'publisher' => 'p', 'completed' => true]);
		$this->assertRedirectedTo('/login');
		$this->call('POST', "/delete/{$game->id}");
		$this->assertRedirectedTo('/login');
	
		Auth::loginUsingId($game->id);

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
		$this->call('POST', "/edit/{$game->id}", ['title' => 't', 'publisher' => 'p', 'completed' => true]);
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
		$game = $this->createGame();
		$this->call('GET', "/delete/{$game->id}");
		$this->assertResponseStatus(200);
	}

	public function testHandleDeletePageActuallyRemovesRecordFromDatabase()
	{
		$game = $this->createGame();
		$this->call('POST', "/delete/{$game->id}");
		$this->assertEquals(DB::table('games')->count(), 0);
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testDeletePageReponseIs404WhenGameDoesNotExist()
	{
		$this->call('GET', "/delete/999");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testHandleDeletePageReponseIs404WhenGameDoesNotExist()
	{
		$this->call('POST', "/delete/999");
	}

	public function testHandleDeletePageRedirectsToIndexAfterRemovingRecordFromDatabase()
	{
		$game = $this->createGame();
		$this->call('POST', "/delete/{$game->id}");
		$this->assertRedirectedTo('/');
	}

	public function testCreatePageExists()
	{
		$this->call('GET', '/create');
		$this->assertResponseStatus(200);
	}

	public function testHandleCreatePageActuallyCreatesGame()
	{
		$user = $this->createUserAndLogHimIn();
		$gameInput = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false];
		$this->call('POST', '/create', $gameInput);
		$this->assertEquals(Game::all()->count(), 1);
	}

	public function testHandleCreateShowErrorsWhenGameDoesNotValidate()
	{
		$gameInput = ['title' => 't'];
		$this->call('POST', '/create', $gameInput);
		$this->assertContains('errors', strtolower($this->client->getResponse()->getContent()));	
	}

	public function testHandleCreateFillValidVieldsWhenThereAreErrorsInOtherFields()
	{
		$gameInput = ['title' => 't'];
		$this->call('POST', '/create', $gameInput);
		$this->assertContains('value="t"', strtolower($this->client->getResponse()->getContent()));	

		$gameInput = ['publisher' => 'p'];
		$this->call('POST', '/create', $gameInput);
		$this->assertContains('value="p"', strtolower($this->client->getResponse()->getContent()));

		$gameInput = ['completed' => true];
		$this->call('POST', '/create', $gameInput);
		$this->assertContains('checked="checked"', strtolower($this->client->getResponse()->getContent()));
	}

	public function testEditPageExists()
	{
		$game = $this->createGame();
		Auth::loginUsingId($game->owner);

		$this->call('GET', "/edit/{$game->id}");
		$this->assertResponseStatus(200);
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testEditPageResponseIs404WhenGameDoesNotExist()
	{
		$this->call('GET', "/edit/999");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testEditPageResponseIs404WhenGameDoesNotBelongToUser()
	{
		$game1 = $this->createGame();
		$game2 = $this->createGame();

		Auth::loginUsingId($game1->owner);

		$this->call('GET', "/edit/{$game2->id}");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testHandleEditPageResponseIs404WhenGameDoesNotExist()
	{
		$this->call('POST', "/edit/999");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testHandleEditPageResponseIs404WhenGameDoesNotBelongToUser($value='')
	{
		$game1 = $this->createGame();
		$game2 = $this->createGame();

		Auth::loginUsingId($game1->owner);

		$this->call('POST', "/edit", ['id' => $game2->id, 'title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false]);
	}

	public function testHandleEditPageActuallyUpdatesGame()
	{
		$game = $this->createGame();

		$input = ['title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->call('POST', "/edit/{$game->id}", $input);

		$gameUpdated = Game::find($game->id);

		$this->assertEquals('Test 2', $gameUpdated->title);
		$this->assertEquals('Publisher 2', $gameUpdated->publisher);
		$this->assertEquals(true, $gameUpdated->completed);
	}

	public function testHandleEditPageRedirectsToIndexAfterUpdatingGame()
	{
		$game = $this->createGame();

		$input = ['title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->call('POST', "/edit/{$game->id}", $input);

		$this->assertRedirectedTo('/');
	}

	public function testHandleEditShowErrorsWhenGameDoesNotValidate()
	{
		$game = $this->createGame();

		$gameInput = ['completed' => false];
		$this->call('POST', "/edit/{$game->id}", $gameInput);
		$this->assertContains('errors', strtolower($this->client->getResponse()->getContent()));	
	}

	public function createUserAndLogHimIn()
	{
		$faker = FakerFactory::create();

		$user = new User();
		$user->email = $faker->email;
		$user->password = Hash::make($faker->name);
		$user->save();

		Auth::logout();
		Auth::loginUsingId($user->id);

		return $user;
	}

	public function createGame()
	{
		$faker = FakerFactory::create();

		$user = $this->createUserAndLogHimIn();
		$game = new Game();
		$game->title = $faker->name;
		$game->publisher = $faker->name;
		$game->completed = true;
		$game->owner = $user->id;
		$game->save();

		return $game;
	}
}