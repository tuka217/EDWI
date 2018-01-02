<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 10.12.17
 * Time: 09:09
 */

namespace AppBundle\Math;


use AppBundle\Helper\DictionaryHelper;
use AppBundle\Helper\DirScanner;
use AppBundle\Helper\WordsSorter;
use NumPHP\Core\NumArray;

class ResemblanceCalculator
{
    /**
     * @var DictionaryHelper
     */
    private $dictionaryHelper;

    /**
     * @var DirScanner
     */
    private $dirScanner;

    /**
     * @var WordsSorter
     */
    private $wordsSorter;

    /**
     * @var array
     */
    private $resemblanceResult;

    /**
     * @var array
     */
    private $filesSortedWords;

    /**
     * @param DictionaryHelper $dictionaryHelper
     * @param DirScanner $dirScanner
     */
    public function __construct(DictionaryHelper $dictionaryHelper, DirScanner $dirScanner, WordsSorter $wordsSorter)
    {
        $this->dictionaryHelper = $dictionaryHelper;
        $this->dirScanner = $dirScanner;
        $this->wordsSorter = $wordsSorter;

        $this->resemblanceResult = [];
        $this->filesSortedWords = [];
    }

    public function runResemblanceCalculations()
    {
        $this->createFilesSortedWords();
        $this->createResemblanceOfFiles();
    }

    public function returnTenTheMostCommon()
    {
        $copy = $this->resemblanceResult;
        asort($copy);

        return array_slice ($copy, 0, 10);
    }

    public function returnTenTheMostDifferent()
    {
        $copy = $this->resemblanceResult;
        arsort($copy);

        return array_slice($copy, 0, 10);
    }

    private function createResemblanceOfFiles()
    {
        foreach ($this->filesSortedWords as $key1 => $sortedWordsFormFile1) {
            foreach ($this->filesSortedWords as $key2 => $sortedWordsFormFile2) {
                $this->calculateTheResemblanceOfTwoFiles($sortedWordsFormFile1, $sortedWordsFormFile2, $key1, $key2);
            }
        }
    }

    private function createFilesSortedWords()
    {
        $filesNames = $this->dirScanner->scanDir();

        foreach ($filesNames as $fileName) {
            $this->filesSortedWords[$fileName] = $this->wordsSorter->sortWords($fileName);
        }
    }

    private function calculateTheResemblanceOfTwoFiles($sortedWordsFromFile1, $sortedWordsFromFile2, $fileOneName, $fileTwoName)
    {
        $vectorForFile1 = new NumArray($this->createVectorForFile($sortedWordsFromFile1));
        $vectorForFile2 =  new NumArray($this->createVectorForFile($sortedWordsFromFile2));

        if (!array_key_exists($fileOneName . "-" . $fileTwoName, $this->resemblanceResult) &&
            !array_key_exists($fileTwoName . "-" . $fileOneName , $this->resemblanceResult)) {
            $this->resemblanceResult[$fileOneName . "-" . $fileTwoName] = $this->calculateCosinus($vectorForFile1, $vectorForFile2);
        }
    }

    private function calculateCosinus(NumArray $vector1, NumArray $vector2)
    {
        $counter = clone($vector1);
        $vector2Copy = clone($vector2);

        $counter->dot($vector2Copy);

        $lengthOfVector1 = $this->vectorLength($vector1);
        $lengthOfVector2 = $this->vectorLength($vector2);

        return $counter->getData() / ($lengthOfVector1 * $lengthOfVector2);
    }

    private function createVectorForFile($sortedWordsFromFile)
    {
        $dictionary = $this->dictionaryHelper->createDictionary();
        $vector = [];
        $sizeOfLoadedFile = count($sortedWordsFromFile);

        foreach ($dictionary as $word) {

            if (array_key_exists($word, $sortedWordsFromFile)) {
                $vector[$word] = $this->calculateWordWeight($sortedWordsFromFile[$word], $sizeOfLoadedFile);
            } else {
                $vector[$word] = 0;
            }
        }

        return $vector;
    }

    private function calculateWordWeight($appearanceOfWord, $amountOfWords)
    {
        return $appearanceOfWord / $amountOfWords;
    }

    private function vectorLength(NumArray $vector)
    {
        $result = 0;

        foreach ($vector->getData() as $value) {
            $result += $value * $value;
        }

        return sqrt($result);
    }
}