<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\ScrapeJobsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ValidateJSONCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new ScrapeJobsCommand());

        $command = $application->find('app:validate-json');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Scraped Jobs JSON Validator', $output);
        $this->assertContains('Jobs loaded from', $output);
        $this->assertContains('JSON is valid', $output);
    }
}
