<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 28.12.17
 * Time: 14:09
 */

namespace AppBundle\Model;


class Hyperlink
{
    /**
     * @var string
     */
    private $httpAddress;

    /**
     * @var int
     */
    private $numberOfRecursion;


    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->httpAddress;
    }

    /**
     * @param string $httpAddress
     */
    public function setAddress(string $httpAddress)
    {
        $this->httpAddress = $httpAddress;
    }

    /**
     * @return int
     */
    public function getNumberOfRecursion()
    {
        return $this->numberOfRecursion;
    }

    /**
     * @param int $numberOfRecursion
     */
    public function setNumberOfRecursion(int $numberOfRecursion)
    {
        $this->numberOfRecursion = $numberOfRecursion;
    }
}
