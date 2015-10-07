<?php
/**
 * Contains the class which defines the scrape command.
 *
 * @package GrocerySiteScraper
 * @author matteo
 */

namespace TechnicalTest;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


use TechnicalTest\Curl;
use TechnicalTest\Scraper;


/**
 * Class which handles the configuration/execution of the scraping command.
 *
 * @package GrocerySiteScraper
 * @author matteo
 */
class ScrapeCommand extends Command
{
    /**
     * Adds the scraping command to the commands available in the application.
     */
    protected function configure()
    {
        $this
            ->setName('scrapeGrocerySite')
            ->setDescription("Scrapes Sainsbury’s grocery site")
        ;
    }

    /**
     * Handles the execution of the command and prints out the command result.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $urlToBeScraped = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";

        $scraper = new Scraper(new Curl(), $urlToBeScraped);
        
        $text = $scraper->scrape();

        $output->writeln($text);
    }
}