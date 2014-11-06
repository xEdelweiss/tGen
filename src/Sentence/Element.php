<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:45
 */

namespace xedelweiss\tGen\Sentence;


/**
 * Class Element
 *
 * @package xedelweiss\tGen
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 */
class Element
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
        return in_array($this->type, [Element::WORD, Element::BISPACED_ELEMENT]);
    }

    /**
     * @return bool
     */
    public function hasSpaceAfter()
    {
        return in_array($this->type, [Element::WORD, Element::BISPACED_ELEMENT, Element::POSTSPACED_ELEMENT]);
    }
}