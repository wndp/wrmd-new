<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface Badgeable
{
    /**
     * Get the object's badge text.
     *
     * @return string
     */
    public function badgeText(): Attribute;

    /**
     * Get the object's badge color
     *
     * @return string
     */
    public function badgeColor(): Attribute;
}
