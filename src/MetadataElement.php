<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 22:09
 */

namespace xedelweiss\tGen;

/**
 * Class MetadataElement
 * Represents info about word with statistical data
 *
 * @package xedelweiss\tGen
 */
class MetadataElement
{
    protected $canonized = null;
    protected $upperCaseCount = 0;
    protected $lowerCaseCount = 0;
    protected $countOverall = 0;

    public function __construct(Word $word)
    {
        $this->canonized = $word->canonized();
        $this->addNewEncounter($word);
    }

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

    public function isUpperCase()
    {
        return $this->upperCaseCount > $this->lowerCaseCount;
    }

    public function isLowerCase()
    {
        return !$this->isUpperCase();
    }
}