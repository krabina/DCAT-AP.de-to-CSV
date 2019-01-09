<?php

namespace Ives;

use Symfony\Component\Console as Console;
use GuzzleHttp\Client as Client;

/**
 * Class GetAllPackagesCommand
 *
 * @author Markus Wallisch <markus.wallisch@interactives.eu>
 */
class GetAllPackagesCommand extends AbstractCommand
{
    protected $input;
    protected $output;

    protected $max = INF;

    protected $total = 0;

    protected $processor;

    protected $outputDirectory = 'output';

	public function __construct($name = null)
    {
        parent::__construct($name);

        if (!file_exists($this->outputDirectory)) {
            mkdir($this->outputDirectory);
        }

        $this->processor = new PackagesToCsv($this->outputDirectory);

        $this->setDescription('Main script');
        $this->setHelp('Get all the package info from OpenNrw');
        $this->addOption('max', 'm', Console\Input\InputOption::VALUE_REQUIRED, 'INT Maximum number of packages to process.', null);
        $this->addOption('offset', 'o', Console\Input\InputOption::VALUE_REQUIRED, 'INT Api Request offset', 0);
        $this->addOption('limit', 'l', Console\Input\InputOption::VALUE_REQUIRED, 'INT Api Request limit', 50);
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        if ($this->input->getOption('max')) {
            $this->max = intval($this->input->getOption('max'));
        }

        $client = new Client();

        $url = "https://open.nrw/api/3/action";
        $action = "current_package_list_with_resources";
        $arguments = [
            'limit' => $this->input->getOption('limit'),
            'offset' => $this->input->getOption('offset')
        ];

        $this->cycle($client, $url . '/' . $action, $arguments);

        // kick off the processor
        $this->processor->process();

        $this->writeInfo($this->output,"Command exit.");
    }

    protected function cycle($client, $url, $arguments) {
        $res = $client->request('GET', $url, [
            'query' => $arguments
        ]);

        if (!$this->checkResult($res, $url)) {
            $this->writeInfo($this->output,"Command exit with errors.");
        }

        $json = json_decode($res->getBody());

        $packages = $json->result;
        $count = count($packages);

        if (!$count) {
            $this->writeInfo($this->output,"No more packages to process.");
            return;
        }

        $finished = false;

        foreach($packages as $package) {
            // exit if we reached maximum number of packages
            if ($this->total >= $this->max) {
                $finished = true;
                break;
            }

            $this->processPackage($package);
            $this->total++;
        }

        if ($this->total >= $this->max) {
            $this->writeInfo($this->output,"Reached maximum number of packages to process.");
            $finished = true;
        }

        if ($count < $arguments['limit']) {
            $this->writeInfo($this->output,"Last cycle.");
            $finished = true;
        }

        // if we are not finished, cycle again ...
        if (!$finished) {
            // build in a delay, so we do not bombard the api with requests
            $this->writeInfo($this->output,"Waiting...");
            sleep(10);

            $nextArguments = [ 'limit' => $arguments['limit'], 'offset' => ($arguments['offset'] + $arguments['limit']) ];
            $this->cycle($client, $url, $nextArguments);
        }
    }

    protected function processPackage($package) {
        $this->writeInfo($this->output,"Reading package " . ($this->total + 1));

        $this->processor->collect($package);
    }

    protected function checkResult($res, $url) {
        if (!$res->getStatusCode() == 200) {
            $this->writeError($this->output,"API call to " . $url . " failed. Status Code 200 was expected, received " . $res->getStatusCode() . " instead");
            return false;
        }

        $json = json_decode($res->getBody());

        if (!$json->success) {
            $this->writeError($this->output,"API call to " . $url . " failed. (success=false was returned)");
            return false;
        }

        return true;
    }

    protected function buildApiRequestUrl($base, $action, $arguments) {
        $url = $base . $action;
        $url.= count($arguments) ? '?' : '';

        foreach($arguments as $key => $value) {
            $url.= $key . '=' . $value;
        }

        return $url;
    }
}