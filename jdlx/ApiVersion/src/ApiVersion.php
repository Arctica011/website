<?php

namespace Jdlx\ApiVersion;

class ApiVersion
{
    protected $version = 1;

    public function parse($version)
    {
        $version = str_replace("v", "", $version);
        if (!is_numeric($version)) {
            return false;
        }
        return $version;
    }

    public function set($version)
    {
        $versionNumber = $version;
        if (!is_numeric($versionNumber)) {
            $versionNumber = $this->parse($versionNumber);
        }

        if (!$versionNumber) {
            throw new \InvalidArgumentException("Invalid version $version");
        }

        $this->version = $versionNumber;
    }

    /**
     * @return int
     */
    public function number()
    {
        return $this->version;
    }

    /**
     * @param $number
     * @return bool
     */
    public function from($number)
    {
        return $this->version >= $number;
    }

    /**
     * @param $number
     * @return bool
     */
    public function before($number)
    {
        return $this->version <= $number;
    }
}
