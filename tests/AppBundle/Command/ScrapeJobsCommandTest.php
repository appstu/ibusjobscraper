<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\ScrapeJobsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScrapeJobsCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new ScrapeJobsCommand());

        $command = $application->find('app:scrape-jobs');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Job Scraper', $output);
        $this->assertContains('Content retrieved', $output);
        $this->assertContains('Scraped jobs saved to', $output);
    }
}
