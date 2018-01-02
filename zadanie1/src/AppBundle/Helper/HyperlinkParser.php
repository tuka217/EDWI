<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 16.12.17
 * Time: 14:16
 */

namespace AppBundle\Helper;


class HyperlinkParser
{
    private $dirScanner;
    private $projectDir;

    public function __construct(DirScanner $dirScanner, $projectDir)
    {
        $this->dirScanner = $dirScanner;
        $this->projectDir = $projectDir;
    }

    public function saveToHyperLinkFile($parentFileName, $pageUrl, $fileName = '')
    {
        if ($fileName === '') {
            $loadedHtmlFile = file_get_contents($this->projectDir . "/html/" . $parentFileName . '.html');
        } else {
            $loadedHtmlFile = file_get_contents($this->projectDir . "/html/" . $fileName . '.html');
        }

        $parsedHtmlFile = $this->stripAllWhichIsNotHyperLink($loadedHtmlFile);
        $this->saveInProperFile($parsedHtmlFile, $parentFileName, $pageUrl);
    }

    private function stripAllWhichIsNotHyperLink($loadedHtmlFile)
    {
        $loadedHtmlFile = preg_replace("/<script.*<\/script>/U"," ",$loadedHtmlFile);
        $loadedHtmlFile = preg_replace("/<style.*<\/style>/U"," ",$loadedHtmlFile);
        $loadedHtmlFile = preg_replace("/<annotation.*<\/annotation>/U"," ",$loadedHtmlFile);
        $loadedHtmlFile = preg_replace("/<link\b.rel=\"stylesheet.*\/>/U"," ",$loadedHtmlFile);
        $loadedHtmlFile = preg_replace("/<a\b.href=\"mailto.*<\/a>/U", "", $loadedHtmlFile);
        $loadedHtmlFile = preg_replace('/&amp;/U', "&", $loadedHtmlFile);
        preg_match_all("/href=\".*\"/U", $loadedHtmlFile, $matches);

        $loadedHtmlFile = "";

        foreach ($matches[0] as $match) {
            $loadedHtmlFile = $loadedHtmlFile . $match . PHP_EOL;
        }

        $loadedHtmlFile = preg_replace('/href=\"/U', "", $loadedHtmlFile);
        $loadedHtmlFile = preg_replace('/\"/U', "", $loadedHtmlFile);

        /** usuwa podwojny backslash bo to linki zewmnetrze z pelnym adresem */
        $loadedHtmlFile = preg_replace('/\/\//U', "", $loadedHtmlFile);

        return $loadedHtmlFile;
    }

    /**
     * @param string $links
     * @param string $fileName
     * @param string $pageUrl
     */
    private function saveInProperFile($links, $fileName, $pageUrl)
    {
        $splittedLinks = $this->splitLinksIntoCategories($links, $pageUrl);
        file_put_contents($this->projectDir  . '/hyperlink/internal/' . $fileName . '.html', $splittedLinks['internal'], FILE_APPEND);
        file_put_contents($this->projectDir  . '/hyperlink/external/' . $fileName . '.html', $splittedLinks['external'], FILE_APPEND);
    }

    /**
     * @param string $links
     * @param string $pageUrl
     *
     * @return array
     */
    private function splitLinksIntoCategories($links, $pageUrl)
    {
        $internalLinks = [];
        $externalLinks = [];
        $arrayOfLinks  = explode(PHP_EOL, $links);

        $host = parse_url($pageUrl, PHP_URL_HOST);
        $scheme = parse_url($pageUrl, PHP_URL_SCHEME) ."://";

        foreach ($arrayOfLinks as $link) {
            if ($link === '') {
                continue;
            }

            if (preg_match('/^[\/|\#].*$/', $link)) {
                $internalLinks[] = $this->addFirstPartOfInternalLink($link, $scheme, $host, $pageUrl) . "\n";
                continue;
            }

            if ($this->checkIfInternal($link, $host)) {
                $internalLinks[] = $this->addFirstPartOfInternalLink($link, $scheme, $host, $pageUrl) . "\n";
                continue;
            }

            $externalLinks[] = $this->parseExternalLink($link) . "\n";
        }

        return ['internal' => $internalLinks, 'external' => $externalLinks];
    }

    /**
     * @param string $link
     * @param string $scheme
     * @param string $host
     * @param string $pageUrl
     *
     * @return string
     */
    private function addFirstPartOfInternalLink($link, $scheme, $host, $pageUrl)
    {
        if (strpos($link, 'android') !== false) {
            return $this->parseAndroidHyperlink($link);
        }

        if (!empty(preg_match("/(https:).\p{L}/", $link))) {
            return preg_replace("/(https:)/", "https://", $link);
        }

        if (!empty(preg_match("/^[\p{L}]+[\.][\p{L}]+[\.]*[\p{L}]*$/", $link))) {
              return $scheme . $link;
        }

        if (!empty(preg_match("/(http:).\p{L}/", $link))) {
            return preg_replace("/(http:)/", "http://", $link);
        }

        if (strpos($link, '#') === 0) {
            return $pageUrl . $link;
        }

        if (strpos($link, $host) !== false && strpos($link, 'http:') === false && strpos($link, 'https:') === false) {

            return $scheme . $link;
        }

        return $scheme . $host . $link;
    }

    /**
     * @param string $link
     *
     * @return string
     */
    private function parseExternalLink($link)
    {
        if (strpos($link, 'android') !== false) {
            $link = $this->parseAndroidHyperlink($link);
        }

        $link = $this->addSchemaToUrl($link);

        return $link;
    }

    /**
     * @param string $link
     * @param string $host
     *
     * @return bool
     */
    private function checkIfInternal($link, $host)
    {
        $hostIp = gethostbyname($host);

        if (strpos($link, 'android') !== false) {
            $link = $this->parseAndroidHyperlink($link);
        }

        $link = $this->addSchemaToUrl($link);

        $hyperlinkHost = gethostbyname(parse_url($link, PHP_URL_HOST));

        return $hostIp === $hyperlinkHost;
    }

    /**
     * @param string $hyperlink
     *
     * @return string
     */
    private function parseAndroidHyperlink($hyperlink)
    {
        $parsedLink = preg_replace("/android-app:.*\/(http|https)\//", "", $hyperlink);

        if (strpos($hyperlink, 'http') !== false) {
            return 'http://' . $parsedLink;
        }

        return 'https://'.$parsedLink;
    }

    /**
     * @param string $hyperlink
     *
     * @return string
     */
    private function addSchemaToUrl($hyperlink)
    {
        if (strpos($hyperlink, 'https') !== false) {
            return 'https://' .  preg_replace("/(http:|https:)(\/\/)?/", "", $hyperlink);
        }

        return 'http://'.  preg_replace("/(http:|https:)(\/\/)?/", "", $hyperlink);
    }
}
