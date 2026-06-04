<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

Route::get('/', function () {
    $host = Request::getHost();

    if (str_contains($host, 'pro')) {
        return redirect('/admin');
    }
    return redirect('/app');
});


Route::controller(EventController::class)->group(function () {
    Route::get('/events/{slug}/template/{id}',           'index');
    Route::post('/events/{slug}/guest/{gid}/wishes',     'sendWishes')->name('event.wishes');
    Route::post('/events/{slug}/guest/{gid}/rsvp',       'rsvpReply')->name('event.rsvp');
    Route::get('/events/{slug}/guest/{gid}/donation',    'getDonationStatus')->name('event.donation.status');
    Route::post('/events/{slug}/guest/{gid}/donation',   'saveDonation')->name('event.donation');
});
Route::get('/admin/clear-category', function () {
    session()->forget('main_category_id');
    // session()->forget('main_category_id');
    return redirect('/admin/select-category');
});