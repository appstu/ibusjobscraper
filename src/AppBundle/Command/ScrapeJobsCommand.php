<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;


class ScrapeJobsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:scrape-jobs')
             ->setDescription('Scrapes jobs.')
             ->setHelp('This command allows you to scrape iBus job offers page');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Job Scraper',
            '===========',
            '',
        ]);

        // check if URL parameter is defined
        if ($this->getContainer()->hasParameter('jobs_url')) {
            $url = $this->getContainer()->getParameter('jobs_url');

            // setup Guzzle6 client
            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $url);
            if ($response->getStatusCode() < 400) {
                $contents = $response->getBody()->getContents();

                $output->writeln('Content retrieved.');

                // use a DOM Crawler for convenient parsing
                $crawler = new Crawler($contents);
                if ($crawler) {
                    $result = $crawler->filter('.career > .widget')->each(function (Crawler $node, $i) {
                        // we could describe an entity, but in this case StdClass seems a sufficient and lightweight option
                        $obj = new \StdClass();
                        $title = $node->filter('.media-heading')->text();
                        if ($title) {
                            $obj->title = $title;

                            $location = $node->filter('.location')->text();
                            if ($location) {
                                $obj->location = $location;

                                $apply_link = $node->filter('.btn')->attr('href');
                                if ($apply_link) {
                                    $obj->apply_link = $apply_link;

                                    $date = $node->filter('.date')->text();
                                    if ($date) $obj->date = $date;

                                    $description = $node->filter('article')->text();
                                    if ($description) {
                                        $description = trim(str_ireplace('apply for this position', '', $description));
                                        $obj->description = $description;
                                    }

                                    return $obj;
                                }
                            }
                        }

                        return null;
                    });

                    // check if jobs_storage parameter is defined
                    if ($this->getContainer()->hasParameter('jobs_storage')) {
                        $file_path = $this->getContainer()->getParameter('jobs_storage');
                        $file_system = new Filesystem();
                        if (!$file_system->exists($file_path)) {
                            $file_system->mkdir($file_path);
                        }
                        $file_system->dumpFile($file_path . '/jobs.json', json_encode($result));
                        $output->writeln('Scraped jobs saved to ' . $file_path . '/jobs.json');
                    } else {
                        $output->writeln('Missing jobs_storage in parameters.yml!');
                    }

                }

            } else {
                $output->writeln('Unable to read data from ' . $url);
                $output->writeln('HTTP status code ' . $response->getStatusCode());
            }
        } else {
            $output->writeln('Missing jobs_url in parameters.yml!');
        }


    }
}