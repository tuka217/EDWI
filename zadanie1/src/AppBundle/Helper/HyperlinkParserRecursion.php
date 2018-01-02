<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 27.12.17
 * Time: 18:50
 */

namespace AppBundle\Helper;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;

class HyperlinkParserRecursion
{

    /**
     * @var FileSaver
     */
    private $fileSaver;

    /**
     * @var HyperlinkParser
     */
    private $hyperlinkParser;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @param FileSaver $fileSaver
     * @param HyperlinkParser $hyperlinkParser
     * @param string $projectDir
     */
    public function __construct(FileSaver $fileSaver, HyperlinkParser $hyperlinkParser, $projectDir)
    {
        $this->fileSaver = $fileSaver;
        $this->hyperlinkParser = $hyperlinkParser;
        $this->projectDir = $projectDir;
    }

    /**
     * @param string $pageAddress
     * @param string $parentPageAddress
     */
    private function getHyperlinksFromPage($pageAddress, $parentPageAddress)
    {
        $client = new Client();
        $res = $client->request('GET', $pageAddress, []);

        /** @var StreamInterface $streamedBody */
        $streamedBody = $res->getBody();

        $fileName = preg_replace("/[^A-Za-z0-9]/", "", $pageAddress);

        $this->fileSaver->saveToHtmlFile($streamedBody, $fileName);

        $parentFileName = preg_replace("/[^A-Za-z0-9]/", "", $parentPageAddress);

        $this->hyperlinkParser->saveToHyperLinkFile($parentFileName, $pageAddress, $fileName);
    }

    /**
     * @param string  $fileName
     * @param int $numberOfRecursion
     * @param string $parentPageAddress
     */
    public function loadInternalFile($fileName, $numberOfRecursion, $parentPageAddress)
    {
        $links = file_get_contents($this->projectDir  . '/hyperlink/internal/' . $fileName . '.html');
        $arrayOfLinks  = explode(PHP_EOL, $links);

        for ($i = 0 ; $i < $numberOfRecursion; $i++) {
            if (array_key_exists($i, $arrayOfLinks)) {
                $this->getHyperlinksFromPage($arrayOfLinks[$i], $parentPageAddress);
            }
        }

    }
}