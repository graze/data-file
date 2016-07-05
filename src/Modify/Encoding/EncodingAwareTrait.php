<?php

namespace Graze\DataFile\Modify\Encoding;

trait EncodingAwareTrait
{
    /** @var string */
    protected $encoding ;

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }
}
