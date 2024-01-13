<?php

namespace MasudZaman\Trails;

use Illuminate\Http\Request;

interface TrailerInterface
{
    /**
     * Calculate a trail (identifier) for the request.
     *
     * Note that this method should be a "pure function" in the sense that any subsequent call to this method
     * should return the same string.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function trail(Request $request): string;
}
