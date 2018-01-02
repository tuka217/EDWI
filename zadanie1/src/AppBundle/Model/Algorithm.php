<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 03.12.17
 * Time: 17:35
 */

namespace AppBundle\Model;

class Algorithm
{
    /**
     * @var string
     */
    private $k;

    /**
     * @var string
     */
    private $thresh;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getK()
    {
        return $this->k;
    }

    /**
     * @param string $k
     */
    public function setK($k)
    {
        $this->k = $k;
    }

    /**
     * @return string
     */
    public function getThresh()
    {
        return $this->thresh;
    }

    /**
     * @param string $thresh
     */
    public function setThresh($thresh)
    {
        $this->thresh = $thresh;
    }
}