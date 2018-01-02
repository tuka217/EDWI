<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 30.12.17
 * Time: 14:25
 */

namespace AppBundle\Model;


class Query
{
    /**
     * @var string 
     */
    private $keyWords;

    /**
     * @return string
     */
    public function getKeyWords()
    {
        return $this->keyWords;
    }

    /**
     * @param string $keyWords
     */
    public function setKeyWords($keyWords)
    {
        $this->keyWords = $keyWords;
    }
}
