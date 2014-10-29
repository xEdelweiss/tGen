<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29.10.2014
 * Time: 21:36
 */

namespace xedelweiss\tGen;


class Metadata {

    protected $data = [];

    /**
     * @param Word $word
     */
    public function addWord(Word $word)
    {
        if (!$this->isWordExists($word)) {
            $this->data[$word->canonized()] = [
                'upperCaseCount' => $word->isUpperCase() ? 1 : 0,
                'lowerCaseCount' => $word->isLowerCase() ? 1 : 0,
                'count'          => 1,
            ];
        } else {
            $this->data[$word->canonized()]['upperCaseCount'] += $word->isUpperCase() ? 1 : 0;
            $this->data[$word->canonized()]['lowerCaseCount'] += $word->isLowerCase() ? 1 : 0;
            $this->data[$word->canonized()]['count'] += 1;
            // а,   о,   э,   и,   у,   ы,   е,   ё,   ю, я
        }
    }

    /**
     * @param Word $word
     * @throws \Exception
     */
    public function getWordMetadata(Word $word)
    {
        if (!$this->isWordExists($word)) {
            throw new \Exception('Word ' . $word->canonized() . ' not found');
        }

        return $this->data[$word->canonized()];
    }

    /**
     * @param Word $word
     * @return bool
     */
    protected function isWordExists(Word $word)
    {
        return isset($this->data[$word->canonized()]);
    }
}