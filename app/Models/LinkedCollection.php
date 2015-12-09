<?php namespace Highcore\Models;

class LinkedCollection extends Collection
{
    /**
     * Used to indicate the identifier for the next page from a continuous result set
     * @var string
     */
    public $nextToken;
}