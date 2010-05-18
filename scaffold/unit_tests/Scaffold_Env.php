<?php

include '_load.php';

class EnvironmentTests extends UnitTestCase
{
	function testDocumentRoot()
	{
		$this->assertTrue(realpath($_SERVER['DOCUMENT_ROOT']));
	}
}