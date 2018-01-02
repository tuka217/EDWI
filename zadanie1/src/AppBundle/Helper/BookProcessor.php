<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 28.12.17
 * Time: 22:31
 */

namespace AppBundle\Helper;


use AppBundle\Model\Book;
use Ivory\LuceneSearchBundle\Model\LuceneManager;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class BookProcessor
{
    /**
     * @var DirScanner
     */
    private $dirScanner;

    private $luceneManager;

    public function __construct(DirScanner $dirScanner, LuceneManager $luceneManager)
    {
        $this->dirScanner = $dirScanner;
        $this->luceneManager = $luceneManager;
    }

    public function parseBooks($pathToDir)
    {
        $files = $this->dirScanner->scanExternalDir($pathToDir);
        foreach ($files as $file) {
            $this->saveAsXmlFile(file_get_contents($pathToDir . "/" . $file));
        }
    }

    private function saveAsXmlFile($contentOfFile)
    {
        $arrayOfContent = $this->unsetGutenbergInfo($contentOfFile);
        $book = $this->createBook($arrayOfContent);

        // Request an index
        $index = $this->luceneManager->getIndex('indentifier1');

        // Create a new document
                $document = new Document();
                $document->addField(Field::text('content', $book->getContent(), $book->getEncoding()));
                $document->addField(Field::text('title', $book->getTitle(), $book->getEncoding()));

        // Add your document to the index
                $index->addDocument($document);

        // Commit your change
                $index->commit();

        // If you want you can optimize your index
              //  $index->optimize();
    }

    private function createBook($arrayOfContent)
    {
        $book  = new Book();

        foreach ($arrayOfContent as $key => $value) {
            if (strpos($value, 'Title:') !== false) {
                $title = preg_replace("/Title:/", "", $value);

                for ($i = $key + 1; ;$i++) {

                    if (strpos($arrayOfContent[$i],"Author:") !== false) {
                        break;
                    }

                    $title = $title . " " . $arrayOfContent[$i];
                    unset($arrayOfContent[$i]);
                }

                $book->setTitle(trim($title));
                unset($arrayOfContent[$key]);
            }

            if (strpos($value, 'Character set encoding:') !== false) {
                $book->setEncoding($this->determineEncoding($value));
            }
        }

        $book->setContent(implode("", $arrayOfContent));

        return $book;
    }

    /**
     * @param string $contentOfFile
     *
     * @return array
     */
    private function unsetGutenbergInfo($contentOfFile)
    {
        $arrayOfContent  = explode(PHP_EOL, $contentOfFile);

        $endKey  = 0;
        foreach ($arrayOfContent as $key => $value) {
            if (strpos($value, 'Title:') !== false) {
                $endKey = $key;
                break;
            }
        }

        foreach ($arrayOfContent as $key => $value) {
            if ($key >= 0 && $key < $endKey) {
                unset($arrayOfContent[$key]);
                continue;
            }

            if ((strpos($value, 'End of Project Gutenberg') !== false) || (strpos($value,"End of the Project Gutenberg") !== false)) {
                $firstKey = $key;
                break;
            }
        }

        $keys = array_keys($arrayOfContent);
        $lastElementKey = array_pop($keys);

        if (isset($firstKey)) {
            for ($i = $firstKey; $i < $lastElementKey; $i++) {
                unset($arrayOfContent[$i]);
            }
        }

        return $arrayOfContent;
    }

    private function determineEncoding($value)
    {
        $encoding  = trim(preg_replace("/Character set encoding:/", "", $value));

        if (strpos($encoding, 'ISO-646-US') !== false) {
            preg_match("/\((.*?)\)/", $encoding, $outputArray);
            $encoding = $outputArray[1];
        }

        return $encoding;
    }

}