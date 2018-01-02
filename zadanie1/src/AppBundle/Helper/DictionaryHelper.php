<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 10.12.17
 * Time: 09:28
 */

namespace AppBundle\Helper;


class DictionaryHelper
{
    /**
     * @var DirScanner
     */
    private $dirScanner;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param DirScanner $dirScanner
     * @param string $rootDir
     */
    public function __construct(DirScanner $dirScanner, $rootDir)
    {
        $this->dirScanner = $dirScanner;
        $this->rootDir = $rootDir;
    }

    public function createDictionary()
    {
        $filesNames = $this->dirScanner->scanDir();

        $words = [];

        foreach ($filesNames as $fileName) {
            $fileContent = $this->loadFileContent($fileName);
            $words = array_merge($words, $fileContent);
        }

        $sortedWords = array_count_values($words);
        $dictionary = [];

        foreach ($sortedWords as $key => $value) {
            $dictionary[$key] = $key;
        }

        asort($dictionary);

        file_put_contents($this->rootDir ."/src/dictionary/dictionary.txt", '<?php $arr = ' . var_export($dictionary, true) . ';');

        return $dictionary;
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