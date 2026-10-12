<?php
// An admin route with no authorization gate. AuthorizationCoverageAnalyzer
// reports this as high, which is what makes the smoke test's blocking case
// blocking. Do not "fix" it.
Route::get('/admin/purge', [PurgeController::class, 'purge']);
