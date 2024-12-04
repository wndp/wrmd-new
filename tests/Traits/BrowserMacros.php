<?php

namespace Tests\Traits;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Before;

trait BrowserMacros
{
    #[Before]
    public function helperMacros()
    {
        Browser::macro('clickLinkWhenVisible', function ($link = null, $seconds = null) {
            return $this->waitForLink($link, $seconds)->clickLink($link);
        });
    }
}
