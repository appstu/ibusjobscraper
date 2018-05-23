<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JsonSchema\Validator as JsonSchemaValidator;

class ValidateJSONCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:validate-json')
            ->setDescription('Validates scraped JSON.')
            ->setHelp('This command allows you to validate scraped iBus job offers JSON to schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Scraped Jobs JSON Validator',
            '===========================',
            '',
        ]);

        // check if jobs_storage parameter is defined
        if ($this->getContainer()->hasParameter('jobs_storage')) {
            $file_path = $this->getContainer()->getParameter('jobs_storage');

            $file_system = new Filesystem();
            if ($file_system->exists($file_path . '/jobs.json')) {
                $data = json_decode(file_get_contents($file_path . '/jobs.json'));

                $output->writeln('Jobs loaded from ' . $file_path . '/jobs.json');

                // Validate
                if ($file_system->exists(realpath('./src/AppBundle/Resources/schema.json'))) {
                    $schema = json_decode(file_get_contents(realpath('./src/AppBundle/Resources/schema.json')));

                    $validator = new JsonSchemaValidator;

                    for ($i = 0; $i < count($data); $i++) {
                        $result = $validator->validate($data[$i], $schema);

                        switch ($result) {
                            case JsonSchemaValidator::ERROR_ALL:
                                $output->writeln('JSON and schema is invalid');
                                return JsonSchemaValidator::ERROR_ALL;
                                break;
                            case JsonSchemaValidator::ERROR_SCHEMA_VALIDATION:
                                $output->writeln('Schema is invalid');
                                return JsonSchemaValidator::ERROR_SCHEMA_VALIDATION;
                                break;
                            case JsonSchemaValidator::ERROR_DOCUMENT_VALIDATION:
                                $output->writeln('JSON is invalid for the item ' . $i);
                                return JsonSchemaValidator::ERROR_DOCUMENT_VALIDATION;
                                break;
                            default:
                        }
                    }
                    // If we passed till here - the JSON data is fine
                    $output->writeln('JSON is valid');
                } else {
                    $output->writeln('Missing schema.json in ' . realpath('./src/AppBundle/Resources/schema.json'));
                }
            } else {
                $output->writeln('Missing jobs.json in ' . $file_path);
            }

        } else {
            $output->writeln('Missing jobs_storage in parameters.yml!');
        }

    }
}