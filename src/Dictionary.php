<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:21
 */

namespace xedelweiss\tGen;

use xedelweiss\tGen\Metadata\Metadata;

/**
 * Class Dictionary
 *
 * @package xedelweiss\tGen
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 */
class Dictionary
{
    const ENCODING = 'UTF-8';
    const DEPTH = 3;
    const MINIMAL_WORDS_IN_SAMPLE_SENTENCE = 5;
    const WORDS_ELEMENT = '<words>';
    public $structure = [];

    /**
     * @var Metadata
     */
    public $metadata = null;
    protected $samples = [];

    public function __construct()
    {
        $this->metadata = new Metadata();
    }

    /**
     * @param $content
     * @return $this
     */
    public function addSample($content)
    {
        $this->samples[] = $content;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function saveToFile($path)
    {
        $serialized = serialize([
            'samples' => $this->samples,
            'structure' => $this->structure,
            'metadata' => $this->metadata
        ]);
        file_put_contents($path, $serialized);

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function loadFromFile($path)
    {
        $serialized = file_get_contents($path);
        $data = unserialize($serialized);

        $this->samples = $data['samples'];
        $this->structure = $data['structure'];
        $this->metadata = $data['metadata'];

        return $this;
    }

    /**
     * @param int $depth
     * @param int $minimalWordsInSentence
     * @return $this
     */
    public function compile($depth = self::DEPTH, $minimalWordsInSentence = self::MINIMAL_WORDS_IN_SAMPLE_SENTENCE)
    {
        // a b c d e
        $text = implode("\n", $this->samples);
        $text = $this->preProcess($text);
        $sentences = $this->splitToSentences($text, $minimalWordsInSentence);

        // a b c e
        // b c d e
        foreach ($sentences as $sentence) {
            $previousWords = [];

            $words = $this->splitToWords($sentence);
            foreach ($words as $word) {
                $word = new Word($word);

                // @todo handle case when all chars are upper

                // add metadata
                $this->metadata->addWord($word);

                // update previous words
                $previousWords[] = $word->canonized();
                if (count($previousWords) > $depth) {
                    array_shift($previousWords);
                }

                // add structure
                $this->addToStructure($previousWords);
            }

            // c d e
            // d e
            while (count($previousWords) > 1) {
                array_shift($previousWords);

                $this->addToStructure($previousWords);
            }
        }

        return $this;
    }

    /**
     * @param $text
     * @return string
     */
    protected function preProcess($text)
    {
        $text = preg_replace('/[<>\[\]()]/u', '', $text); // remove some chars
        $text = preg_replace('/(\n|\r){2,}/u', '\1', $text); // remove multiple EOLs
        $text = preg_replace('/(\n|\r)(?! *[a-zа-яіїє\'’`])/u', '. ', $text); // split sentences on multiple lines
        $text = preg_replace('/(\n|\r)/u', ' ', $text); // replace EOLs with spaces
        $text = preg_replace('/ +/u', ' ', $text); // replace multiple spaces
        $text = preg_replace('/-+/u', '-', $text); // replace multiple hyphens
        return $text;
    }

    /**
     * @param $text
     * @param int $minimalWordsInSentence
     * @return array
     */
    protected function splitToSentences($text, $minimalWordsInSentence = self::MINIMAL_WORDS_IN_SAMPLE_SENTENCE)
    {
        $sentences = preg_split('/([.?!:"]| - )+/u', $text); // split sentences
        foreach ($sentences as &$sentence) {
            $sentence = trim($sentence);
        }
        $sentences = array_filter(
            $sentences,
            function ($item) use ($minimalWordsInSentence) {
                return (!empty($item) && mb_substr_count($item, ' ',
                        self::ENCODING) > $minimalWordsInSentence - 1);
            }
        );

        return $sentences;
    }

    /**
     * @param $text
     * @return array
     */
    protected function splitToWords($text)
    {
        $text = preg_replace('/[^a-zа-яіїє\'`’-]/ui', ' ', $text); // remove all, except letters, hyphens, apostrophes
        $text = preg_replace('/ +/u', ' ', $text); // replace multiple spaces

        $words = preg_split('/ /ui', $text);

        $words = array_filter($words);
        $words = array_filter($words, function($word){
            return preg_match('/^[a-zа-яіїє]/ui', $word); // skip without letters at the beginning
        });

        return $words;
    }

    /**
     * @param array $path
     */
    protected function addToStructure($path)
    {
        $currentElement = &$this->structure;
        foreach ($path as $pathElement) {
            // if no <words> container - create
            if (!isset($currentElement[self::WORDS_ELEMENT])) {
                $currentElement[self::WORDS_ELEMENT] = [];
            }

            // if no pathElement sub-structure
            if (!isset($currentElement[$pathElement])) {
                $currentElement[$pathElement] = [];
            }

            // if no pathElement in <words>
            if (!in_array($pathElement, $currentElement[self::WORDS_ELEMENT])) {
                $currentElement[self::WORDS_ELEMENT][] = $pathElement;
            }

            $currentElement = &$currentElement[$pathElement];
        }
    }

}