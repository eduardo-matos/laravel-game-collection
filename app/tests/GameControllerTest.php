<?php

class GameControllerTest extends TestCase
{

	public function testIndexPageExists()
	{
		// act
		$this->client->request('GET', '/');

		// assert
		$this->assertResponseStatus(200);
	}

	public function testIndexShowsGameList()
	{
		// arrange
		$games = [
			['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'],
			['title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:14', 'updated_at' => '2012-12-12 12:12:15'],
		];
		DB::table('games')->insert($games);

		// act
		$crawler = $this->client->request('GET', '/');

		// assert
		$content = strtolower($this->client->getResponse()->getContent());
		$this->assertContains('test 1', $content);
		$this->assertContains('test 2', $content);
		$this->assertContains('publisher 1', $content);
		$this->assertContains('publisher 2', $content);
		$this->assertContains('yes', $content);
		$this->assertContains('no', $content);
	}

	public function testDeletePageExists()
	{
		// arrange
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		// act
		$this->client->request('GET', "/delete/{$id}");

		// assert
		$this->assertResponseStatus(200);
	}

	public function testHandleDeletePageActuallyRemovesRecordFromDatabaseExists()
	{
		// // arrange
		$games = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($games);

		// act
		$this->client->request('POST', "/delete/{$id}");

		// assert
		$this->assertEquals(DB::table('games')->count(), 0);
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testDeletePageReponseIs404WhenGameDoesNotExist()
	{
		// act
		$this->client->request('GET', "/delete/999");
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testHandleDeletePageReponseIs404WhenGameDoesNotExist()
	{
		// act
		$this->client->request('POST', "/delete/999");
	}

	public function testHandleDeletePageRedirectsToIndexWhenAfterRemovingRecordFromDatabase()
	{
		// arrange
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		// act
		$crawler = $this->client->request('POST', "/delete/{$id}");

		// assert
		$this->assertRedirectedTo('/');
	}

	public function testCreatePageExists()
	{
		$this->client->request('GET', '/create');
		$this->assertResponseStatus(200);
	}

	public function testHandleCreateActuallyCreatesGame()
	{
		$gameInput = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->client->request('POST', '/create', $gameInput);
		$this->assertEquals(DB::table('games')->count(), 1);
	}

	public function testUpdatePageExists()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
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

	public function testHandleUpdatePageActuallyUpdatesGame()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$input = ['id' => $id, 'title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->client->request('POST', "/edit", $input);
		
		$game = Game::find($id);
		$this->assertEquals('Test 2', $game->title);
		$this->assertEquals('Publisher 2', $game->publisher);
		$this->assertEquals(true, $game->completed);
	}

	public function testHandleUpdatePageRedirectsToIndexAfterUpdatingGame()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$input = ['id' => $id, 'title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->client->request('POST', "/edit", $input);
		
		$this->assertRedirectedTo('/');
	}
}