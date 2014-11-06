<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 21:36
 */

namespace xedelweiss\tGen\Metadata;

use xedelweiss\tGen\Word;


/**
 * Class Metadata
 * @package xedelweiss\tGen\Metadata
 */
class Metadata
{

    protected $data = [];

    /**
     * @param Word $word
     */
    public function addWord(Word $word)
    {
        if (!$this->isWordExists($word)) {
            $wordMetadata = new Element($word);
            $this->data[$word->canonized()] = $wordMetadata;
        } else {
            $wordMetadata = $this->getWordMetadata($word);
            $wordMetadata->addNewEncounter($word);
        }

        $this->setWordMetadata($wordMetadata);
    }

    /**
     * @param Word $word
     * @return bool
     */
    protected function isWordExists(Word $word)
    {
        return isset($this->data[$word->canonized()]);
    }

    /**
     * @param Word $word
     * @throws \Exception
     * @return Element
     */
    public function getWordMetadata(Word $word)
    {
        if (!$this->isWordExists($word)) {
            throw new \Exception('Word ' . $word->canonized() . ' not found');
        }

        return $this->data[$word->canonized()];
    }

    /**
     * @param Element $metadataElement
     */
    protected function setWordMetadata(Element $metadataElement)
    {
        $word = $metadataElement->word();
        $this->data[$word->canonized()] = $metadataElement;
    }

    /**
     * @return array
     */
    public function getAllWords()
    {
        return array_keys($this->data);
    }
}