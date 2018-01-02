<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 28.12.17
 * Time: 23:03
 */

namespace AppBundle\Model;


class Directory
{
    /**
     * @var string
     */
    private $pathToDir;

    /**
     * @return string
     */
    public function getPathToDir()
    {
        return $this->pathToDir;
    }

    /**
     * @param string $pathToDir
     */
    public function setPathToDir($pathToDir)
    {
        $this->pathToDir = $pathToDir;
    }
}
