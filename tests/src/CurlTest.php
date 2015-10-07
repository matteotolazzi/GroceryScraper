<?php

use TechnicalTest\Curl;

class CurlTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests that the "getData" function can get site page content and its size
	 */
	public function testGetData()
	{
		$curl = new Curl();

		$data = $curl->getData("http://www.google.com");

		// checking that we got data back
		$this->assertTrue($data != false);
		// checking that we got back an html page
		$this->assertContains('</html>', $data);

		$pageSize = $curl->getLastDownloadSize();

		// checking that we got page size as number
		$this->assertTrue(is_numeric($pageSize) && $pageSize > 0);
	}
}