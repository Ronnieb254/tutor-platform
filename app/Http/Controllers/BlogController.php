<?php

namespace App\Http\Controllers;

/**
 * Redirect shim — /blog has been renamed to /library.
 * All requests are permanently redirected (301) to the new URLs.
 */
class BlogController extends Controller
{
    public function index()
    {
        return redirect()->route('library.index', [], 301);
    }

    public function show(string $slug)
    {
        return redirect()->route('library.show', $slug, 301);
    }
}
