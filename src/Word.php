<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 21:37
 */

namespace xedelweiss\tGen;

/**
 * Class Word
 * Represents info about word without statistical data
 *
 * @package xedelweiss\tGen
 */
class Word
{
    const ENCODING = 'UTF-8';
    const VOWELS = 'аоэиуыеёюя';
    const CASE_ORIGINAL = 'original';
    const CASE_UPPER = 'upper';
    const CASE_LOWER = 'lower';

    /**
     * @var string
     */
    protected $value = null;

    public function __construct($word)
    {
        if (!is_string($word)) {
            throw new \Exception('$word must be string, ' . gettype($word) . ' given');
        }
        $this->value = $word;
    }

    /**
     * @param string $case
     * @return string
     */
    public function value($case = self::CASE_ORIGINAL)
    {
        $result = $this->value;

        if ($case == self::CASE_UPPER) {
            $result = mb_convert_case($result, MB_CASE_TITLE, self::ENCODING);
        } elseif ($case == self::CASE_LOWER) {
            $result = mb_strtolower($result, self::ENCODING);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isUpperCase()
    {
        return $this->getFirstLetter() === mb_strtoupper($this->getFirstLetter(), self::ENCODING);
    }

    /**
     * @return string
     */
    protected function getFirstLetter()
    {
        return mb_substr($this->value, 0, 1, self::ENCODING);
    }

    /**
     * @return bool
     */
    public function isLowerCase()
    {
        return $this->getFirstLetter() === mb_strtolower($this->getFirstLetter(), self::ENCODING);
    }

    /**
     * @return int
     */
    public function getSyllableCount()
    {
        $word = $this->canonized();
        $word = preg_replace('/([' . self::VOWELS . '])+/ui', '\1', $word); // remove doubled vowels

        $onlyVowels = preg_replace('/([^' . self::VOWELS . '])/ui', '', $word);

        $vowelsCount = mb_strlen($onlyVowels, self::ENCODING);
        return $vowelsCount > 0 ? $vowelsCount : 1;
    }

    /**
     * @return string
     */
    public function canonized()
    {
        return mb_strtolower($this->value, self::ENCODING);
    }
} 