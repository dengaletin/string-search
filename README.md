## Installation
`composer require dengaletin/string-search`

## Testing
Install PHPUnit

`composer require --dev phpunit/phpunit ^7`

Run tests

`./vendor/bin/phpunit --bootstrap vendor/autoload.php vendor/sidspears/string-search/tests/`

## Usage example
``` 
<?php
require_once('vendor/autoload.php');
use SidSpears\StringSearcher\StringSearcher;

$stringSearcher = new StringSearcher('config.yaml');
$result = $stringSearcher->findStringAndPosition('file.txt', 'textToFind');

print_r($result);
``` 