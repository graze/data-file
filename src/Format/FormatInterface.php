<?php

namespace Graze\DataFile\Format;

interface FormatInterface
{
    /**
     * Type type of file format
     *
     * @return string
     */
    public function getType();
}
