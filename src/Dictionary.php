<?php
/**
 * User: user
 * Date: 29.10.2014
 * Time: 15:21
 */

namespace xedelweiss\tGen;

const MINIMAL_WORDS_IN_SAMPLE_SENTENCE = 5;

/**
 * Class Dictionary
 *
 * @package xedelweiss\tGen
 * @author Michael Sverdlikovsky <michael.sverdlikovsky@ab-soft.net>
 */
class Dictionary
{
    const ENCODING = 'UTF-8';
    const DEPTH = 3;

    protected $samples = [];
    public $structure = [];
    public $metadata = [];
    const WORDS_ELEMENT = '<words>';

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
        $serialized = serialize(['samples' => $this->samples, 'structure' => $this->structure, 'metadata' => $this->metadata]);
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
        $data       = unserialize($serialized);

        $this->samples   = $data['samples'];
        $this->structure = $data['structure'];
        $this->metadata  = $data['metadata'];

        return $this;
    }

    /**
     * @param int $depth
     * @return $this
     */
    public function compile($depth = self::DEPTH)
    {
        $text      = implode("\n", $this->samples);
        $text      = $this->preProcess($text);
        $sentences = $this->splitToSentences($text);

        foreach ($sentences as $sentence) {
            $previousWords = [];

            $words = $this->splitToWords($sentence);
            foreach ($words as $word) {
                $canonized   = $this->canonizeWord($word);
                $firstLetter = mb_substr($word, 0, 1, self::ENCODING);
                $isUpperCase = $firstLetter === mb_strtoupper($firstLetter, self::ENCODING);
                $isLowerCase = $firstLetter === mb_strtolower($firstLetter, self::ENCODING);

                // @todo handle case when all chars are upper

                // add metadata

                if (!isset($this->metadata[$canonized])) {
                    $this->metadata[$canonized] = [
                        'upperCaseCount' => $isUpperCase ? 1 : 0,
                        'lowerCaseCount' => $isLowerCase ? 1 : 0,
                        'count'          => 1,
                    ];
                } else {
                    $this->metadata[$canonized]['upperCaseCount'] += $isUpperCase ? 1 : 0;
                    $this->metadata[$canonized]['lowerCaseCount'] += $isLowerCase ? 1 : 0;
                    $this->metadata[$canonized]['count'] += 1;
                }

                // update previous words

                $previousWords[] = $canonized;
                if (count($previousWords) > $depth) {
                    array_shift($previousWords);
                }

                // add structure

                $this->addToStructure($previousWords);
            }
        }

        return $this;
    }

    public function getMetadata($word)
    {
        $canonized = $this->canonizeWord($word);

        if (!isset($this->metadata[$canonized])) {
            throw new \Exception('Word ' . $canonized . ' not found');
        }

        return $this->metadata[$canonized];
    }

    /**
     * @param string $word
     * @return string
     */
    public function canonizeWord($word)
    {
        return mb_strtolower($word, self::ENCODING);
    }

    /**
     * @param array $path
     */
    protected function addToStructure($path)
    {
        $currentElement = &$this->structure;
        foreach ($path as $pathElement) {
            if (!isset($currentElement[self::WORDS_ELEMENT])) {
                $currentElement[self::WORDS_ELEMENT] = [];
            }

            if (!isset($currentElement[$pathElement])) {
                $currentElement[$pathElement] = [];
            }

            if (!in_array($pathElement, $currentElement[self::WORDS_ELEMENT])) {
                $currentElement[self::WORDS_ELEMENT][] = $pathElement;
            }

            $currentElement = &$currentElement[$pathElement];
        }
    }

    /**
     * @param $text
     * @return string
     */
    protected function preProcess($text)
    {
        $text = preg_replace('/(\n|\r){2,}/u', '\1', $text); // remove multipe EOLs
        $text = preg_replace('/(\n|\r)(?! *[a-zа-я])/u', '. ', $text); // split sentences on multiple lines
        $text = preg_replace('/(\n|\r)/u', ' ', $text); // replace EOLs with spaces
        $text = preg_replace('/ +/u', ' ', $text); // replace multiple spaces
        return $text;
    }

    /**
     * @param $text
     * @return array
     */
    protected function splitToSentences($text)
    {
        $sentences = preg_split('/([.?!:"]| - )+/u', $text); // split sentences
        foreach ($sentences as &$sentence) {
            $sentence = trim($sentence);
        }
        $sentences = array_filter(
            $sentences,
            function ($item) {
                return (!empty($item) && mb_substr_count($item, ' ', self::ENCODING) > MINIMAL_WORDS_IN_SAMPLE_SENTENCE - 1);
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
        $text = preg_replace('/[^a-zа-я]/ui', ' ', $text); // remove all, except letters
        $text = preg_replace('/ +/u', ' ', $text); // replace multiple spaces

        $words = preg_split('/ /ui', $text);

        return $words;
    }

}