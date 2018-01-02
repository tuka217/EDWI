<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 29.12.17
 * Time: 20:43
 */

namespace AppBundle\Helper;

use Ivory\LuceneSearchBundle\Model\LuceneManager;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Wildcard;

class QueryHelper
{
    /**
     * @var LuceneManager
     */
    private $luceneManager;

    /**
     * @param LuceneManager $luceneManager
     */
    public function __construct(LuceneManager $luceneManager)
    {
        $this->luceneManager = $luceneManager;
    }

    public function findBooks($keyWords)
    {
        $documents = $this->luceneManager->getIndex('indentifier1')->find($keyWords);

        $titles = [];
        foreach ($documents as $document) {
           $titles[] = $document->title;
           $titles[] = $document->content;
        }

        return $titles;
    }
}