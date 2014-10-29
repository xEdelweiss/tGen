<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 20:35
 */

namespace xedelweiss\tGen\Generator;

use xedelweiss\tGen\Dictionary;

class Base {

    /**
     * @var Dictionary
     */
    protected $dictionary = NULL;

    /**
     * @param Dictionary $dictionary
     * @return $this
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;

        return $this;
    }
} 