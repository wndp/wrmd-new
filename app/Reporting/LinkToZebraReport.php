<?php

namespace App\Reporting;

class LinkToZebraReport
{
    protected $title;

    protected $classKey;

    protected $parameters;

    public function __construct($title, $classKey, $parameters = [])
    {
        $this->title = $title;
        $this->classKey = $classKey;
        $this->parameters = $parameters;
    }

    public function __toString()
    {
        $uri = http_build_query($this->parameters);

        return "<link-to-zebra class-key='{$this->classKey}' uri='{$uri}'>{$this->title}</link-to-zebra>";
    }
}
