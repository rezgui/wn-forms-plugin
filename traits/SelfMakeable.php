<?php

namespace Gromit\Forms\Traits;

trait SelfMakeable
{
    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }
}
