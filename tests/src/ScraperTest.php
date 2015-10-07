<?php

use TechnicalTest\Scraper;
use TechnicalTest\Curl;

class ScraperTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Testing that Scraper->scrapeProductDetailsAndBuilProduct function can handle properly the expected
	 * DOMDocument producing an object with the expected properties set.
	 */
	public function testScrapeProductDetailsAndBuilProduct()
	{
		$scraper = new Scraper(new Curl(), 'no url needed in this test');

		$testProductTitle = 'test product title';
		$testProductUnitPrice = 'Â£123.45/unit';
		$testProductDescription = 'peperoni gialli';
		$testSize = 1000;
		$testProductUnitPrice_expected = '123.45';
		$testSize_expected = '1.00kb';

		$dom = new \DOMDocument();

		//
		// Product title
		// 
		// "//div[@class='productTitleDescriptionContainer']/h1"
		
		$titleDiv = $dom->createElement('div');
		$titleDiv->setAttribute('class', 'productTitleDescriptionContainer');
		$titleDiv = $dom->appendChild($titleDiv);

		$titleh1 = $dom->createElement('h1', $testProductTitle);
		$titleh1 = $titleDiv->appendChild($titleh1);

		//
		// Product unit_price
		//
		// "//div[@class='pricing']/p[@class='pricePerUnit']"
		$unitPriceDiv = $dom->createElement('div');
		$unitPriceDiv->setAttribute('class', 'pricing');
		$unitPriceDiv = $dom->appendChild($unitPriceDiv);

		$unitPriceP = $dom->createElement('p', $testProductUnitPrice);
		$unitPriceP->setAttribute('class', 'pricePerUnit');
		$unitPriceP = $unitPriceDiv->appendChild($unitPriceP);

		//
		// Product description
		//
		// "//div[@class='productText']/p"
		$descriptionDiv = $dom->createElement('div');
		$descriptionDiv->setAttribute('class', 'productText');
		$descriptionDiv = $dom->appendChild($descriptionDiv);

		$descriptionP = $dom->createElement('p', $testProductDescription);
		$descriptionP = $descriptionDiv->appendChild($descriptionP);

		//
		// Function under test execution
		//
		$product = $scraper->scrapeProductDetailsAndBuildProduct($dom, 1000);

		//
		// Checking the product returned
		//

		// checking that the product is an object
		$this->assertTrue(is_object($product));
		// checking that the product has all expected properties
		$this->assertObjectHasAttribute('title', $product);
		$this->assertObjectHasAttribute('size', $product);
		$this->assertObjectHasAttribute('unit_price', $product);
		$this->assertObjectHasAttribute('description', $product);
		// checking values of the product properties
		$this->assertEquals($product->title, $testProductTitle);
		$this->assertEquals($product->size, $testSize_expected);
		$this->assertEquals($product->unit_price, $testProductUnitPrice_expected);
		$this->assertEquals($product->description, $testProductDescription);
	}

}