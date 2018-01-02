<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 10.12.17
 * Time: 09:21
 */

namespace AppBundle\Helper;


class WordsSorter
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    public function sortWords($fileName) {
        $words = $this->loadFileContent($fileName);

        $countedValues = array_count_values($words);
        arsort($countedValues);

        return $countedValues;
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
