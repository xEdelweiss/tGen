<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:29
 */

namespace xedelweiss\tGen\Generator;

use xedelweiss\tGen\Dictionary;
use xedelweiss\tGen\Sentence;
use xedelweiss\tGen\SentenceElement;
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
     * @param int $wordsCount
     * @param int $depth
     * @throws \Exception
     * @return Sentence
     */
    public function sentence($wordsCount = 5, $previousWords = [], $depth = Dictionary::DEPTH)
    {
        $result = new Sentence();

        while ($result->wordsCount() < $wordsCount) {
            $canonized = $this->getNext($previousWords);

            // try to replace short words in last position with longer ones
            if ($wordsCount - $result->wordsCount() == 1) {
                if ((new Word($canonized))->getSyllableCount() <= self::MINIMAL_SYLLABLE_COUNT_IN_LAST_WORD) {
                    $maxTries = 3;
                    for ($i = 0; $i < $maxTries; $i++) {
                        $newCanonized = $this->getNext($previousWords); // @todo work with WordsSet+filters instead of this
                        echo '// trying to replace "' . $canonized . '" with "' . $newCanonized . '"' . PHP_EOL;

                        if ((new Word($newCanonized))->getSyllableCount() > self::MINIMAL_SYLLABLE_COUNT_IN_LAST_WORD) {
                            $canonized = $newCanonized;
                            break;
                        }
                    }
                }
            }

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

        $result->addElement(SentenceElement::POSTSPACED_ELEMENT, '.', Sentence::ELEMENT_ADD_REPLACE);

        return $result;
    }
}