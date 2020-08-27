<?php
namespace TheSkyNet\Markov;

class Markov
{
    function generate($text, $order)
    {

        // walk through the text and make the index table for words
        $wordsTable = explode(' ', trim($text));
        $table = [];
         foreach ($wordsTable as $key => $word) {
            $nextWord = "";
            for ($j = 0; $j < $order; $j++) {
                if ($key + $j + 1 != sizeof($wordsTable) - 1){
                    if(isset($wordsTable[$key + $j + 1])){
                        $nextWord .= " " . $wordsTable[$key + $j + 1];
                    }

                }
            }
            if (!isset($table[$word . $nextWord])) {
                $table[$word . $nextWord] = [];
            }
        }

        $tableLength = sizeof($wordsTable);

        // walk the array again and count the numbers
        for ($i = 0; $i < $tableLength - 1; $i++) {
            $word_index = $wordsTable[$i];
            $word_count = $wordsTable[$i + 1];
            if (isset($table[$word_index][$word_count])) {
                $table[$word_index][$word_count] += 1;
            } else {
                $table[$word_index][$word_count] = 1;
            }
        }

        return $table;
    }

    function sentenceBegin($str)
    {
        return $str == ucfirst($str);
    }

    function generateText($length, $table)
    {

        do {
            $word = array_rand($table);
        } while (!$this->sentenceBegin($word));

        $o = $word;

        while (strlen($o) < $length) {
            $neWord = $this->getWeightedWord($table[$word]);

            if ($neWord) {
                $word = $neWord;
                $o .= " " . $neWord;
            } else {
                do {
                    $word = array_rand($table);
                } while (!$this->sentenceBegin($word));
            }
        }


        return $o;
    }


    protected function getWeightedWord($array)
    {
        if (!$array) return false;

        $total = array_sum($array);
        $rand = mt_rand(1, $total);
        foreach ($array as $item => $weight) {
            if ($rand <= $weight) {
                return $item;
            }
            $rand -= $weight;
        }
        return false;
    }
}
