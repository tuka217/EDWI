<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 03.12.17
 * Time: 17:40
 */
namespace AppBundle\Math;

class AlgorithmCalculator
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var int
     */
    private $microtime;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return int
     */
    public function getMicrotime()
    {
        return $this->microtime;
    }

    /**
     * @param int $k
     * @param int $thresh
     * @param string $fileName
     *
     * @return array
     */
    public function calculate($k, $thresh, $fileName)
    {
        $words = $this->loadFileContent($fileName);

        $rustart = microtime();

        $countedValues = array_count_values($words);
        arsort($countedValues);

        $result = [];
        $counter = 0;

        foreach ($countedValues as $word => $appearance) {
            if ($appearance < $thresh) {
                continue;
            }

            $result[$word] = $appearance;
            $counter++;

            if ($counter === $k) {
                break;
            }
        }

        $ru = microtime();

        $this->microtime = $this->calculateMicrotime($ru, $rustart);
        return $result;
    }

    /**
     * @param array $ru
     * @param array $rus
     *
     * @return int
     */
    private function calculateMicrotime($ru, $rus) {
        return $this->microtimeValue($ru) - $this->microtimeValue($rus);
    }

    /**
     * @param $microtime
     *
     * @return int
     */
    private function microtimeValue($microtime){
        list($usec, $sec) = explode(" ", $microtime);
        return $usec;
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    private function loadFileContent($fileName)
    {
        $file = $this->rootDir ."/txt/" .$fileName;
        $content = file_get_contents($file);
        $splitedContent = $pieces = explode(" ", $content);

        foreach ($splitedContent as $key => $word) {
            if("" === $word) {
                unset($splitedContent[$key]);
            }
        }

        return array_values($splitedContent);
    }
}
