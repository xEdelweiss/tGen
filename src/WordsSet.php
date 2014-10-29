<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 22:34
 */

namespace xedelweiss\tGen;

/**
 * Class WordsSet
 * Allows to search by different criteria against array of words
 *
 * @package xedelweiss\tGen
 */
class WordsSet
{

    protected $words = [];

    /**
     * @param array $words
     */
    public function __construct($words)
    {
        $this->words = $words;
    }

    /**
     * @return string
     */
    public function randomWord()
    {
        $index = array_rand($this->words, 1);
        return $this->words[$index];
    }

    /**
     * @param $text
     * @return WordsSet
     */
    public function rhymedTo($text)
    {
        $match = [];

        foreach ($this->words as $word) {
            if (preg_match('/' . $text . '$/ui', $word)) {
                $match[] = $word;
            }
        }

        return new WordsSet($match);
    }

    /**
     * @param $count
     * @return WordsSet
     */
    public function withSyllableCount($count)
    {
        $match = [];

        foreach ($this->words as $word) {
            $word = new Word($word);
            if ($word->getSyllableCount() == $count) {
                $match[] = $word->canonized();
            }
        }

        return new WordsSet($match);
    }

    /**
     * @return array
     */
    public function getAllWords()
    {
        return $this->words;
    }
} 