<?php
/**
 * Contains the class which handles curl requests.
 *
 * @package GrocerySiteScraper
 * @author matteo
 */

namespace TechnicalTest;


/**
 * Handles a curl request and offers methods to get the page requested and its size.
 *
 * @package GrocerySiteScraper
 * @author matteo
 */
class Curl
{

    private $sizeDownload;

    /**
     * Executes a curl request returns the data obtained and sets the $sizeDownload property of the object
     * with the size of the page.
     *
     * @param string $url the URL to request.
     * @return string $data the page obtained in response from the curl request.
     */
    public function getData($url) 
    {

        $ch = curl_init();
        $timeout = 60;
        curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome/22.0.1216.0');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        
        $data = curl_exec($ch);

        // setting the property which represents the last requested page size.
        $this->sizeDownload = curl_getinfo ($ch , CURLINFO_SIZE_DOWNLOAD);

        curl_close($ch);
        return $data;
    }

    /**
     * Returns the $sizeDownload property.
     *
     * @return int the $sizeDownload property of the current instance.
     */
    public function getLastDownloadSize()
    {
        return $this->sizeDownload;
    }


}