subl# open-nrw api consumer
description `todo`

## requirements
1. php >= 5.6
2. composer [https://getcomposer.org/](https://getcomposer.org/)

## install
1. Clone / Download Repository
2. Run `composer install`

## usage
To start the console run:
`php start.php`

## commands

### get:packages
Downloads all available package meta data from
https://open.nrw/api/3/action/current_package_list_with_resources

### get:packages -m 5
Same as above, but limited to max. 5 packages

### get:packages -h
Display all options for get:packages (there is also a limit and offset option)

Main package information is written to
* `output/packages.csv`

Group and tag information is available in
* `output/groups.csv`
* `output/tags.csv`