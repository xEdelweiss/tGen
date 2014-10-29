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
     * @param array $path
     * @return string
     */
    public function getNext($path = [])
    {
        $currentElement = &$this->dictionary->structure;
        foreach ($path as $pathElement) {
            if (!isset($currentElement[$pathElement]) || !isset($currentElement[$pathElement][Dictionary::WORDS_ELEMENT])) {
                array_shift($path);
                return $this->getNext($path);
            }

            $currentElement = &$currentElement[$pathElement];
        }

        $words = $currentElement[Dictionary::WORDS_ELEMENT];
        $index = array_rand($words, 1);

        return $words[$index];
    }

    /**
     * @param int $wordsCount
     * @param int $depth
     * @throws \Exception
     * @return Sentence
     */
    public function sentence($wordsCount = 5, $depth = Dictionary::DEPTH)
    {
        $result = new Sentence();

        $previousWords = [];

        while ($result->wordsCount() < $wordsCount) {
            $canonized = $this->getNext($previousWords);
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