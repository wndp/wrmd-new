<?php

namespace App\Listing;

class ListGroup
{
    public $title;

    public $lists;

    public $visibility = true;

    public function __construct($title, array $lists)
    {
        $this->title = $title;
        $this->lists = $lists;
    }

    /**
     * Set the visibility of the report group.
     */
    public function setVisibility(bool $visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }
}
