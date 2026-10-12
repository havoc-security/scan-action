<?php
namespace App;

// No routes and no controller actions, so nothing reaches high severity and
// the scan exits 0. The analyzers still report project-level findings below
// the fail-on threshold, which is the point: the clean case proves the action
// does not fail a build over them.
class Plain
{
    public function hi(): string
    {
        return 'hi';
    }
}
