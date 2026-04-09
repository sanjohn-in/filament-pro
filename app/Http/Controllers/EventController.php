<?php

namespace App\Http\Controllers;

use App\Models\Admin\Configuration;
use App\Models\Admin\Guest;
use App\Models\Admin\MainCategory;
use App\Models\Admin\Theme;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display wedding invitation preview
     * 
     * Route: /events/{slug}/template/{id}
     */
    public function index(Request $request, $slug, $id)
    {
        $theme = Theme::findOrFail($id);
        $guest = $request->query('gid')
        ? Guest::findOrFail($request->query('gid'))
        : null;
        $event = MainCategory::where('slug', $slug)->firstOrFail();
    
        $music = Configuration::where('slug', 'music')->value('value');
        $wishes = Guest::where('main_category_id', $event->id)
        ->whereNotNull('note')
        ->where('note', '!=', '')
        ->get();

    
        return view('welcome', compact('event', 'guest', 'theme', 'music', 'wishes'));
    }

    /**
     * Save guest wishes by keynote
     * Route: POST /events/{slug}/guest/{gid}/wishes
     */
    public function sendWishes(Request $request, $slug, $gid)
    {
        $request->validate(['wishes' => 'required|string|max:1000']);

        $guest = Guest::findOrFail($gid);
        $guest->update(['note' => $request->input('wishes')]);

        return response()->json(['success' => true]);
    }

    /**
     * Save RSVP reply and return personalised message
     * Route: POST /events/{slug}/guest/{gid}/rsvp
     */
    public function rsvpReply(Request $request, $slug, $gid)
    {
        $request->validate(['status' => 'required|in:yes,no']);

        $guest = Guest::findOrFail($gid);
        $isAttending = $request->input('status') ;
        $guest->update(['is_attending' => $isAttending]);

        // Personalised message using the saved value
        $message = $guest->is_attending == 'yes'
            ? '🎉 អរគុណ ' . $guest->name . '! យើងពិតជារីករាយណាស់ដែលអ្នកអាចចូលរួមជាមួយយើងបាន។'
            : '💛 យើងយល់ហើយ ' . $guest->name . '។ សូមអរគុណសម្រាប់ការប្រាប់យើងឱ្យដឹង។';

        return response()->json([
            'success'      => true,
            'is_attending' => $guest->is_attending,
            'message'      => $message,
        ]);
    }

}