<?php

class GameControllerTest extends TestCase
{

	public function testIndexPageExists()
	{
		$this->client->request('GET', '/');
		$this->assertResponseStatus(200);
	}

	public function testIndexPageShowsGameList()
	{
		$games = [
			['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'],
			['title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:14', 'updated_at' => '2012-12-12 12:12:15'],
		];
		DB::table('games')->insert($games);

		$crawler = $this->client->request('GET', '/');

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
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$this->client->request('GET', "/delete/{$id}");

		$this->assertResponseStatus(200);
	}

	public function testHandleDeletePageActuallyRemovesRecordFromDatabase()
	{
		$games = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
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
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
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

	public function testHandleEditPageActuallyUpdatesGame()
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

	public function testHandleEditPageRedirectsToIndexAfterUpdatingGame()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$input = ['id' => $id, 'title' => 'Test 2', 'publisher' => 'Publisher 2', 'completed' => true, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$this->client->request('POST', "/edit", $input);
		
		$this->assertRedirectedTo('/');
	}

	public function testHandleEditShowErrorsWhenGameDoesNotValidate()
	{
		$game = ['title' => 'Test 1', 'publisher' => 'Publisher 1', 'completed' => false, 'created_at' => '2012-12-12 12:12:12', 'updated_at' => '2012-12-12 12:12:13'];
		$id = DB::table('games')->insertGetId($game);

		$gameInput = ['completed' => false, 'id' => $id];
		$this->client->request('POST', '/edit', $gameInput);
		$this->assertContains('errors', strtolower($this->client->getResponse()->getContent()));	
	}
}