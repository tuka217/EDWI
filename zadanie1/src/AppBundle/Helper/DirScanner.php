<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 03.12.17
 * Time: 18:59
 */

namespace AppBundle\Helper;

class DirScanner
{
    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @param string $directory
     *
     * @return array
     */
    public function scanDir($directory = '/txt')
    {
        $directory =  scandir($this->projectDir . $directory);
        $scanned_directory = array_diff($directory, array('..', '.'));

        $result  = [];

        foreach ($scanned_directory as $fileName) {
            $result[$fileName] = $fileName;
        }

        return $result;
    }

    /**
     * @param string $directory
     *
     * @return array
     */
    public function scanExternalDir($directory)
    {
        $directory =  scandir($directory);
        $scanned_directory = array_diff($directory, array('..', '.'));

        $result  = [];

        foreach ($scanned_directory as $fileName) {
            $result[$fileName] = $fileName;
        }

        unset($result[".directory"]);

        return $result;
    }

}