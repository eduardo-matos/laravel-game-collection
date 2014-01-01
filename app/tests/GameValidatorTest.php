<?php

use Validators\Game as GameValidator;

class GameValidatorTest extends TestCase
{

	public function testTitleAndPublisherFieldsAreRequired()
	{
		$input1 = array('title' => 't');
		$input2 = array('publisher' => 'p');

		$validator1 = new GameValidator($input1);
		$validator2 = new GameValidator($input2);

		$validator1->passes();
		$validator2->passes();

		$this->assertEquals(1, count($validator1->errors));
		$this->assertEquals(1, count($validator2->errors));
	}

}
