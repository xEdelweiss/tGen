<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 20:35
 */

namespace xedelweiss\tGen\Generator;

use xedelweiss\tGen\Dictionary;
use xedelweiss\tGen\WordsSet;

/**
 * Class Base
 *
 * @package xedelweiss\tGen\Generator
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 */
abstract class Base
{

    /**
     * @var Dictionary
     */
    protected $dictionary = null;

    /**
     * @param Dictionary $dictionary
     * @return $this
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;

        return $this;
    }

    /**
     * @param $path
     * @return string|WordsSet
     */
    public function getNextWordsSet($path)
    {
        $currentElement = &$this->dictionary->structure;
        foreach ($path as $pathElement) {
            if (!isset($currentElement[$pathElement]) || !isset($currentElement[$pathElement][Dictionary::WORDS_ELEMENT])) {
                return new WordsSet([]);
            }

            $currentElement = &$currentElement[$pathElement];
        }

        $words = $currentElement[Dictionary::WORDS_ELEMENT];
        $wordsSet = new WordsSet($words);

        return $wordsSet;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function simplify($path)
    {
        array_shift($path);

        return $path;
    }
} 