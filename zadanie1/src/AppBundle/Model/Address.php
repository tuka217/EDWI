<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 26.11.17
 * Time: 09:32
 */

namespace AppBundle\Model;

class Address
{
    /**
     * @var string
     */
    private $httpAddress;

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
    public function setAddress($httpAddress)
    {
        $this->httpAddress = $httpAddress;
    }
}
