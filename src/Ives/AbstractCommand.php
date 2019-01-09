<?php

namespace Ives;

use Symfony\Component\Console as Console;

/**
 * Class AbstractCommand
 *
 * @author Markus Wallisch <markus.wallisch@interactives.eu>
 */
abstract class AbstractCommand extends Console\Command\Command
{
    public function __construct($name) {
        parent::__construct($name);
    }

    public static function writeInfo(Console\Output\OutputInterface $output, $message) {
        $output->writeln("<info>$message</info>");
    }

    public static function writeComment(Console\Output\OutputInterface $output, $message) {
        $output->writeln("<comment>$message</comment>");
    }

    public static function writeQuestion(Console\Output\OutputInterface $output, $message) {
        $output->writeln("<question>$message</question>");
    }

    public static function writeError(Console\Output\OutputInterface $output, $message) {
        $output->writeln("<error>$message</error>");
    }
}