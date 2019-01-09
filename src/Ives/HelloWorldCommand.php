<?php

namespace Ives;

use Ives\AbstractCommand;
use Symfony\Component\Console as Console;

/**
 * Class HelloWorldCommand
 *
 * Just included as an example starting point for a new command.
 *
 * @author Markus Wallisch <markus.wallisch@interactives.eu>
 */
class HelloWorldCommand extends AbstractCommand
{
	public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setDescription('Outputs HELLO WORLD');
        $this->setHelp('Outputs hello world message.');
        //$this->addArgument('name', Console\Input\InputArgument::OPTIONAL, 'The name to output to the screen', 'World');
        //$this->addOption('more', 'm', Console\Input\InputOption::VALUE_NONE, 'Tell me more');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->writeInfo($output, 'HELLO WORLD');
    }
}