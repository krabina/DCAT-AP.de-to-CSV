<?php

namespace Ives;

use League\Csv\Writer as Writer;

/**
 * Class PackagesToCsv
 *
 * @author Markus Wallisch <markus.wallisch@interactives.eu>
 */
class PackagesToCsv
{
    protected $packages;

    protected $csvData = [
        'packages' => [],
        'tags' => [],
        'groups' => [],
    ];

    protected $headers = [];

    public function __construct($outputDir) {
        // as urged by league/csv documentation:
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        $this->outputDir = $outputDir;

        $this->initCsvHeaders();
    }

    protected function initCsvHeaders() {
        $this->headers['packages'] = [
            'id',
            'license_title',
            'maintainer',
            'maintainer_email',
            'revision_timestamp',
            'metadata_created',
            'metadata_modified',
            'author',
            'author_email',
            'version',
            'creator_user_id',
            'type',
            'name',
            'url',
            'notes',
            'owner_org',
            'license_url',
            'title',
            'revision_id',
            'organization.id',
            'organization.title',
            'organization.name'
        ];

        $this->headers['tags'] = [
            'package_id',
            'id',
            'display_name',
            'name'
        ];

        $this->headers['groups'] = [
            'package_id',
            'id',
            'display_name',
            'name',
            'title'
        ];
    }

    /**
     * Takes one package and processes it.
     *
     * @param $package
     */
    public function collect($package) {
        // super simple approach:
        // just collect all data in a big array
        // at the end, dump the csvs

        $this->packages[] = $package;
    }

    public function process() {
        // build up internal data structure
        foreach($this->packages as $package) {
            $this->processPackage($package);
        }

        // export to csv
        $csv = Writer::createFromPath($this->outputDir . "/packages.csv", "w");
        $csv->setDelimiter(';');
        $csv->insertOne($this->headers['packages']);
        $csv->insertAll($this->csvData['packages']);


        $csv = Writer::createFromPath($this->outputDir . "/tags.csv", "w");
        $csv->setDelimiter(';');
        $csv->insertOne($this->headers['tags']);
        $csv->insertAll($this->csvData['tags']);

        $csv = Writer::createFromPath($this->outputDir . "/groups.csv", "w");
        $csv->setDelimiter(';');
        $csv->insertOne($this->headers['groups']);
        $csv->insertAll($this->csvData['groups']);
    }

    /**
     * Process and convert the data for end-consumer (the person who reads the written csvs)
     */
    protected function processPackage($package) {

        // write the head
        $this->buildPackageDataArray($package);

        // and some details
        $this->buildTagsDataArray($package);

        // and some more details
        $this->buildGroupsDataArray($package);

    }

    protected function buildPackageDataArray($package) {
        $r = [];

        $r[] = $package->id;
        $r[] = $package->license_title;
        $r[] = $package->maintainer;
        $r[] = $package->maintainer_email;
        $r[] = $package->revision_timestamp;
        $r[] = $package->metadata_created;
        $r[] = $package->metadata_modified;
        $r[] = $package->author;
        $r[] = $package->author_email;
        $r[] = $package->version;
        $r[] = $package->creator_user_id;
        $r[] = $package->type;
        $r[] = $package->name;
        $r[] = $package->url;
        $r[] = preg_replace( "/\r|\n/", "", $package->notes );    // kill the line breaks
        $r[] = $package->owner_org;
        $r[] = property_exists($package,'license_url') ? $package->license_url : '';
        $r[] = $package->title;
        $r[] = $package->revision_id;

        if (property_exists($package, 'organization')) {
            $r[] = $package->organization->id;
            $r[] = $package->organization->title;
            $r[] = $package->organization->name;
        }

        $this->csvData['packages'][] = $r;
    }

    protected function buildTagsDataArray($package) {
        if (!property_exists($package, 'tags') || !is_array($package->tags)) {
            return;
        }

        foreach($package->tags as $tag) {
            $r = [];

            $r[] = $package->id;        // keep the package reference
            $r[] = $tag->id;
            $r[] = $tag->display_name;
            $r[] = $tag->name;

            $this->csvData['tags'][] = $r;
        }
    }

    protected function buildGroupsDataArray($package) {
        if (!property_exists($package,'groups') || !is_array($package->groups)) {
            return;
        }

        foreach($package->groups as $group) {
            $r = [];

            $r[] = $package->id;        // keep the package reference
            $r[] = $group->id;
            $r[] = $group->display_name;
            $r[] = $group->name;
            $r[] = $group->title;

            $this->csvData['groups'][] = $r;
        }
    }
}