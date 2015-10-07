<?php
/**
 * Contains the main class of the application
 *
 * @package GrocerySiteScraper
 * @author matteo
 */

namespace TechnicalTest;

use TechnicalTest\Curl;

/**
 * Class which contains the methods needed by the application to scrape the main grocery site page
 * and the pages of every product.
 *
 * @package GrocerySiteScraper
 * @author matteo
 */
class Scraper
{
	private $mainUrl;
	private $scraped;
	private $curl;
	private $lastCurlDownloadSize;

	public function __construct(Curl $curl, $url)
	{
		$this->curl = $curl;
		$this->mainUrl = $url;
		$this->scraped = new \StdClass();
		$this->scraped->results = array();
		$this->scraped->total = 0;
	}

	/**
	 * Gets the DOMDocument of the products page, calls the function which handles each product page scraping
	 * and then returns the final result of the scraping process.
	 * 
	 * @return string the JSON representation of the products array produced by the scraping process.
	 */
	public function scrape()
	{
		$pageDom = $this->getDom($this->mainUrl);

		if ($pageDom instanceof \DOMDocument)
		{
			$scrapingSuccessful = $this->scrapeProducts($pageDom);

			if (!$scrapingSuccessful)
				echo 'Scraping process failed!' . PHP_EOL;
		}
		return json_encode($this->scraped);
	}

	/**
	 * Scrapes the page to identify product detail links, follows each link
	 * and scrapes the corresponding page to get the products info which are used to populate the Scrapes->scraped
	 * object.
	 *
	 * @param DOMDocument $pageDom the DOMDocument instance representing the page containing the product details.
	 * @return bool false in case of error while trying to identify the products in the page DOMDocument $pageDom.
	 */
	public function scrapeProducts(\DOMDocument $pageDom)
	{
		$pageXpath = new \DOMXpath($pageDom);
		$productsLinks = $pageXpath->query("//div[@class='productInfo']/h3/a/@href");

		if (! $productsLinks instanceof \DOMNodeList)
			return false;

		$total = 0;
        foreach ($productsLinks as $productLink)
        {
        	if (! $productLink instanceof \DOMAttr)
        		continue;

        	$productDom = $this->getDom($productLink->textContent);

        	if (! $productDom instanceof \DOMDocument)
        		continue;

        	$pageSize = $this->lastCurlDownloadSize;
        	$productInfo = $this->scrapeProductDetailsAndBuildProduct($productDom, $pageSize);
        	$total += isset($productInfo->unit_price) ? $productInfo->unit_price : 0;

        	array_push($this->scraped->results, $productInfo);
        }
        $this->scraped->total = number_format($total, 2);

        return true;
	}

	/**
	 * Scrapes a product page detail DOMDocument instance, searches for product attributes and returns an object containing all the 
	 * product details.
	 *
	 * @param DOMDocument $productDom represents the product details page.
	 * @param int $pageSize the size in Bytes of the product details page.
	 * @return object $productInfo the product extracted from the product details page.
	 */
	public function scrapeProductDetailsAndBuildProduct(\DOMDocument $productDom, $pageSize)
	{
		$productInfo = new \StdClass();
		$productXpath = new \DOMXpath($productDom);
		do
		{
			//
			// product title
			//
			$productInfo->title = $this->getProductAttribute($productXpath, "//div[@class='productTitleDescriptionContainer']/h1");

			//
			// product page size
			//
			$productInfo->size = number_format(floatval($pageSize / 1000), 2) . "kb";

			//
			// product unit_price
			//
			$productInfo->unit_price = $this->getProductAttribute($productXpath, "//div[@class='pricing']/p[@class='pricePerUnit']");
			$productInfo->unit_price = str_replace('/unit', '', $productInfo->unit_price);
			$productInfo->unit_price = str_replace('Â£', '', $productInfo->unit_price);
			$productInfo->unit_price = trim($productInfo->unit_price);
			$productInfo->unit_price = number_format(floatval($productInfo->unit_price), 2);

			//
			// product description
			//
			$productInfo->description = $this->getProductAttribute($productXpath, "//div[@class='productText']/p");

		} while(false); 

		return $productInfo;
	}

	/**
	 * Searches the DOMXpath object argument for occurences of the $query argument expecting to find just one occurrence.
	 * Gets the first item in the DOMNodeList and returns its textContent property value.
	 *
	 * @param DOMXpath $productXpath contains informations of one attribute (ex. title, description, ...) of one product. 
	 * @param string $query containes the query (ex. "//div[@class='productText']/p") which will be used to search the attribute's value.
	 */
	private function getProductAttribute(\DOMXpath $productXpath, $query)
	{
		$attribute = '';
		do
		{
			$productAttributeDOMNodeList = $productXpath->query($query);

			if (! $productAttributeDOMNodeList instanceof \DOMNodeList || count($productAttributeDOMNodeList) != 1)
				break;

			$productAttributeDOMElement = $productAttributeDOMNodeList->item(0);

			if (! $productAttributeDOMElement instanceof \DOMElement)
				break;

			$attribute = $productAttributeDOMElement->textContent;

		} while(false);

		return $attribute;
	}

	/**
	 * Executes a curl request to the URL received as argument, sets the class property $lastCurlDownloadSize with the
	 * value of the size of the page obtained as response for the curl request, creates a new DOMDocument object from
	 * the page and returns it.
	 *
	 * @param string $url the URL from which to build the DOMDocument instance to return.
	 * @return DOMDocument instance representing the page obtained from the CURL request.
	 */
	private function getDom($url)
	{
		// curl request
        $siteContent = $this->curl->getData($url);

        if (!$siteContent) 
        {
            echo 'Failed to get site content' . PHP_EOL;
            return false;
        }

        // avoiding bad markup warnings
        libxml_use_internal_errors(true);

        // creating a DOMDocument object from the page
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($siteContent);

        if (!$domDocument) 
        {   
        	// dumping warnings
            foreach (libxml_get_errors() as $error)
            {
                var_dump($error);
            }
            echo 'Error while trying to create DOMDocument object' . PHP_EOL;
        }
        else
        {
        	// setting the size of the page obtained by curl
        	$this->lastCurlDownloadSize = $this->curl->getLastDownloadSize();
        }
        return $domDocument;
	}

}