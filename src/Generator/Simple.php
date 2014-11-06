<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:29
 */

namespace xedelweiss\tGen\Generator;

use xedelweiss\tGen\Dictionary;
use xedelweiss\tGen\Sentence;
use xedelweiss\tGen\Word;
use xedelweiss\tGen\WordsSet;

/**
 * Class Simple
 *
 * @package xedelweiss\tGen\Generator
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 */
class Simple extends Base
{
    const MINIMAL_SYLLABLE_COUNT_IN_LAST_WORD = 1;

    /**
     * @param int $wordsCount
     * @param int $depth
     * @throws \Exception
     * @return Sentence\Container
     */
    public function sentence($wordsCount = 5, $previousWords = [], $depth = Dictionary::DEPTH)
    {
        $result = new Sentence\Container();

        while ($result->wordsCount() < $wordsCount) {
            $nextWordsSet = $this->getNextWordsSet($previousWords)->without($result->getWords());

            if ($nextWordsSet->isEmpty()) {
                $previousWords = $this->simplify($previousWords);
                $result->addElement(Sentence\Element::POSTSPACED_ELEMENT, ',', Sentence\Container::ELEMENT_ADD_SINGLE);
                continue;
            }

            $canonized = $nextWordsSet->randomWord();

            $metadata = $this->dictionary->metadata->getWordMetadata(new Word($canonized));
            $word = $metadata->isUpperCase() || ($result->wordsCount() == 0)
                ? $metadata->word()->value(Word::CASE_UPPER)
                : $metadata->word()->value(Word::CASE_LOWER);

            $result->addWord($word);

            $previousWords[] = $canonized;
            if (count($previousWords) > $depth) {
                array_shift($previousWords);
            }
        }

        $result->addElement(Sentence\Element::POSTSPACED_ELEMENT, '.', Sentence\Container::ELEMENT_ADD_REPLACE);

        return $result;
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
        $wordsSet = new WordsSet($words);

        return $wordsSet->randomWord();
    }

    /**
     * @param $path
     * @return mixed
     *
     * @todo remove
     */
    public function simplify($path)
    {
        array_pop($path);

        return $path;
    }
}