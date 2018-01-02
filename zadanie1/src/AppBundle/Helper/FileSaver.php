<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 27.12.17
 * Time: 18:06
 */

namespace AppBundle\Helper;


class FileSaver
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @param string $projectDir
     */
    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @param string $content
     * @param string $fileName
     */
    public function saveToHtmlFile($content, $fileName)
    {
        $file = $this->projectDir . "/html/" . $fileName . '.html';

        file_put_contents($file, $content);
    }

    /**
     * @param string $fileName
     */
    public function saveToTxtFile($fileName)
    {
        $file = $this->projectDir . "/txt/" . $fileName . '.txt';

        $content = file_get_contents($this->projectDir . "/html/" . $fileName . '.html');
        $content = $this->stripAllTags($content);
        $content = preg_replace('!\s+!', ' ', $content);

        if (mb_detect_encoding($content) !== 'UTF-8') {
            $content = iconv("ISO-8859-2", "UTF-8", $content);
        }

        $content = preg_replace("/[^\p{L}\ ]/u", " ", $content);

        file_put_contents($file, $content);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function stripAllTags($content)
    {
        $content = preg_replace("/\n/"," ",$content);
        $content = preg_replace("/<script.*<\/script>/U"," ",$content);
        $content = preg_replace("/<style.*<\/style>/U"," ",$content);
        $content = preg_replace("/<annotation.*<\/annotation>/U"," ",$content);
        $content = str_replace( '<', ' <', $content);
        $content = str_replace( '>', '> ', $content);
        $content = strip_tags(strtolower($content));

        return $content;
    }
}
