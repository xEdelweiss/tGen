<?php

ini_set('memory_limit', '1024M');
set_time_limit(0);
$debug = TRUE;

require './vendor/autoload.php';

$samples = [];

foreach (new FilesystemIterator('./samples/') as $file) {
    /** @var FilesystemIterator $file */
    if (!$file->isFile() || $file->getFilename() == '.gitkeep') {
        continue;
    }

    $samples[] = $file->getFilename();
}

// init dictionary

$dictionary = new xedelweiss\tGen\Dictionary();

if (!$debug && file_exists('./compiled/dictionary.tgd')) {
    $dictionary->loadFromFile('./compiled/dictionary.tgd');
} else {
    foreach ($samples as $sampleName) {
        $content = file_get_contents('./samples/' . $sampleName);
        $dictionary->addSample($content);
    }

    $dictionary->compile()->saveToFile('./compiled/dictionary.tgd');
}

// init generator

$generator = new xedelweiss\tGen\Generator\Simple();
$generator->setDictionary($dictionary);

// use generator

$sentence = $generator->sentence(10);
echo $sentence;