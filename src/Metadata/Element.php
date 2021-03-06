<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 22:09
 */

namespace xedelweiss\tGen\Metadata;

use xedelweiss\tGen\Word;

/**
 * Class Element
 * Represents info about word with statistical data
 *
 * @package xedelweiss\tGen\Metadata
 */
class Element
{
    protected $canonized = null;
    protected $upperCaseCount = 0;
    protected $lowerCaseCount = 0;
    protected $countOverall = 0;

    /**
     * @param Word $word
     */
    public function __construct(Word $word)
    {
        $this->canonized = $word->canonized();
        $this->addNewEncounter($word);
    }

    /**
     * @param Word $word
     */
    public function addNewEncounter(Word $word)
    {
        $this->upperCaseCount += (int)$word->isUpperCase();
        $this->lowerCaseCount += (int)$word->isLowerCase();
        $this->countOverall += 1;
    }

    /**
     * @return Word
     */
    public function word()
    {
        return new Word($this->canonized);
    }

    /**
     * @return bool
     */
    public function isLowerCase()
    {
        return !$this->isUpperCase();
    }

    /**
     * @return bool
     */
    public function isUpperCase()
    {
        return $this->upperCaseCount > $this->lowerCaseCount;
    }

    /**
     * @return int
     */
    public function getCountOverall()
    {
        return $this->countOverall;
    }
}