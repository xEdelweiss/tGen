<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 21:37
 */

namespace xedelweiss\tGen;

class Word
{
    const ENCODING = 'UTF-8';
    const VOWELS = 'аоэиуыеёюя';

    protected $word = null;

    public function __construct($word)
    {
        if (!is_string($word)) {
            throw new \Exception('$word must be string, ' . gettype($word) . ' given');
        }
        $this->word = $word;
    }

    public function canonized()
    {
        return mb_strtolower($this->word, self::ENCODING);
    }

    public function value()
    {
        return $this->word;
    }

    public function isUpperCase()
    {
        return $this->getFirstLetter() === mb_strtoupper($this->getFirstLetter(), self::ENCODING);
    }

    public function isLowerCase()
    {
        return $this->getFirstLetter() === mb_strtolower($this->getFirstLetter(), self::ENCODING);
    }

    public function getSyllableCount()
    {
        $word = $this->canonized();
        $word = preg_replace('/([' . self::VOWELS . '])+/ui', '\1', $word); // remove doubled vowels

        $onlyVowels = preg_replace('/([^' . self::VOWELS . '])/ui', '', $word);

        return mb_strlen($onlyVowels, self::ENCODING);
    }

    protected function getFirstLetter()
    {
        return mb_substr($this->word, 0, 1, self::ENCODING);
    }
} 