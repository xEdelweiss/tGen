<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:45
 */

namespace xedelweiss\tGen;


/**
 * Class SentenceElement
 *
 * @package xedelweiss\tGen
 * @author Michael Sverdlikovsky <michael.sverdlikovsky@ab-soft.net>
 */
class SentenceElement
{
    const WORD = 'word';
    const BISPACED_ELEMENT = 'bispaced';
    const POSTSPACED_ELEMENT = 'postspaced';
    const UNDEFINED = 'undefined';

    public $type = NULL;
    public $value = NULL;

    /**
     * @param $type
     * @param $value
     */
    public function __construct($type, $value)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function hasSpaceBefore()
    {
        return in_array($this->type, [SentenceElement::WORD, SentenceElement::BISPACED_ELEMENT]);
    }

    /**
     * @return bool
     */
    public function hasSpaceAfter()
    {
        return in_array($this->type, [SentenceElement::WORD, SentenceElement::BISPACED_ELEMENT, SentenceElement::POSTSPACED_ELEMENT]);
    }
}