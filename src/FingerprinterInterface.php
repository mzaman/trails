<?php

namespace MasudZaman\Fingerprints;

use Illuminate\Http\Request;

interface FingerprinterInterface
{
    /**
     * Calculate a fingerprint (identifier) for the request.
     *
     * Note that this method should be a "pure function" in the sense that any subsequent call to this method
     * should return the same string.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function fingerprint(Request $request): string;
}
