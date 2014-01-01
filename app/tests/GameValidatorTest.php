<?php

use Validators\Game as GameValidator;

class GameValidatorTest extends TestCase
{

	public function testTitleAndPublisherAndCompletedFieldsAreRequired()
	{
		$input1 = array('title' => 't');
		$input2 = array('publisher' => 'p');
		$input3 = array('completed' => true);

		$validator1 = new GameValidator($input1);
		$validator2 = new GameValidator($input2);
		$validator3 = new GameValidator($input3);

		$validator1->passes();
		$validator2->passes();
		$validator3->passes();

		$this->assertEquals(2, count($validator1->errors));
		$this->assertEquals(2, count($validator2->errors));
		$this->assertEquals(2, count($validator3->errors));
	}

}
