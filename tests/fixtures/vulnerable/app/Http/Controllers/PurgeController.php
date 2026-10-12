<?php
namespace App\Http\Controllers;

class PurgeController extends Controller
{
    // No $this->authorize(), no Gate::, no middleware on the route.
    public function purge()
    {
        return response()->json(['ok' => true]);
    }
}
