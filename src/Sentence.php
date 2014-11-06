<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:32
 */

namespace xedelweiss\tGen;

/**
 * Class Sentence
 *
 * @package xedelweiss\tGen
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 */
class Sentence
{
    const ELEMENT_ADD_FREE = 'free';
    const ELEMENT_ADD_SINGLE = 'single';
    const ELEMENT_ADD_REPLACE = 'replace';
    /**
     * @var SentenceElement[]
     */
    protected $structure = [];

    /**
     * @return string
     */
    public function __toString()
    {
        $result = '';

        /** @var SentenceElement $previousItem */
        $previousItem = NULL;
        foreach ($this->structure as $item) {
            $addSpace = false;

            if ($item->hasSpaceBefore()) {
                $addSpace = true;
            }

            if (!is_null($previousItem)) {
                $addSpace = $previousItem->hasSpaceAfter();
            }

            if (!$item->hasSpaceBefore()) {
                $addSpace = false;
            }

            if (empty($result)) {
                $addSpace = false;
            }

            $value = $item->value;

            if (empty($result) && $item->type == SentenceElement::WORD) {
                $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
            }

            $result .= $addSpace ? ' ' : '';
            $result .= $value;

            $previousItem = $item;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function wordsCount()
    {
        return count($this->getWords());
    }

    public function syllableCount()
    {
        $words = $this->getWords();
        $result = 0;

        foreach ($words as $word) {
            $result += (new Word($word))->getSyllableCount();
        }

        return $result;
    }

    /**
     * Return only words
     * @return array
     */
    public function getWords()
    {
        $result = [];

        foreach ($this->structure as $element) {
            if ($element->type == SentenceElement::WORD) {
                $result[] = $element->value;
            }
        }

        return $result;
    }

    /**
     * @param string $word
     * @return $this
     */
    public function addWord($word)
    {
        $this->addElement(SentenceElement::WORD, $word);

        return $this;
    }

    /**
     * @param string $type
     * @param string $value
     * @param string $mode
     * @return $this
     */
    public function addElement($type, $value, $mode = self::ELEMENT_ADD_FREE)
    {
        if ($mode == self::ELEMENT_ADD_SINGLE && $this->getLastElement()->type !== SentenceElement::WORD) {
            return $this;
        }

        if ($mode == self::ELEMENT_ADD_REPLACE && $this->getLastElement()->type !== SentenceElement::WORD) {
            array_pop($this->structure);
        }

        $this->structure[] = new SentenceElement($type, $value);
        return $this;
    }

    /**
     * @return SentenceElement
     */
    public function getLastElement()
    {
        if (empty($this->structure)) {
            return new SentenceElement(SentenceElement::UNDEFINED, NULL);
        }

        return $this->structure[count($this->structure)-1];
    }
}