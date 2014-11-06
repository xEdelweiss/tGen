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
    const MODE_EQUAL = '==';
    const MODE_LTE = '<=';
    const MODE_LT = '<';
    const MODE_GTE = '>=';
    const MODE_GT = '>';

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
    public function withSyllableCount($count, $mode = self::MODE_EQUAL)
    {
        $match = [];

        foreach ($this->words as $word) {
            $word = new Word($word);
            $check = eval('return ' . $word->getSyllableCount() . $mode . $count . ';');
            if ($check) {
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

    /**
     * @return int
     */
    public function count()
    {
        return count($this->words);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     * @param array $wordsToSkip
     * @return WordsSet
     */
    public function without($wordsToSkip)
    {
        $match = array_diff($this->words, $wordsToSkip);

        return new WordsSet($match);
    }
} 