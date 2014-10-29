<?php

require './vendor/autoload.php';

$samples = [
    'picnic.txt',
    'grad_obrechennij.txt',
];

// generate dictionary

$dictionary = new xedelweiss\tGen\Dictionary();

foreach ($samples as $sampleName) {
    $content = file_get_contents('./samples/' . $sampleName);
    $dictionary->addSample($content);
}

$dictionary->compile()->saveToFile('./compiled/dictionary.tgd');

// load dictionary

// $dictionary = new xedelweiss\tGen\Dictionary();
// $dictionary->loadFromFile('./compiled/dictionary.tgd');

// init generator

$generator = new xedelweiss\tGen\Generator();
$generator->setDictionary($dictionary);

// use generator

echo $generator->sentence(10);