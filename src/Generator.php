<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:29
 */

namespace xedelweiss\tGen;

/**
 * Class Generator
 *
 * @package xedelweiss\tGen
 * @author Michael Sverdlikovsky <michael.sverdlikovsky@ab-soft.net>
 */
class Generator
{
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

    /**
     * @param int $wordsCount
     * @return Sentence
     */
    public function sentence($wordsCount = 5, $depth = 3)
    {
        $result = new Sentence();

        $previousWords = [];

        while ($result->wordsCount() < $wordsCount) {
            $canonized = $this->dictionary->getNext($previousWords);
            $metadata = $this->dictionary->getMetadata($canonized);
            $word = ($metadata['upperCaseCount'] > $metadata['lowerCaseCount']) || ($result->wordsCount() == 0)
                ? mb_convert_case($canonized, MB_CASE_TITLE, Dictionary::ENCODING)
                : $canonized;

            $result->addWord($word);

            $previousWords[] = $canonized;
            if (count($previousWords) > $depth) {
                array_shift($previousWords);
            }

            var_dump($previousWords);
        }

        $result->addElement(SentenceElement::POSTSPACED_ELEMENT, '.', Sentence::ELEMENT_ADD_REPLACE);

        return $result;
    }
}