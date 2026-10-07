<?php

namespace App\Http\Controllers;

use App\Models\Admin\Configuration;
use App\Models\Admin\Donation;
use App\Models\Admin\Guest;
use App\Models\Admin\MainCategory;
use App\Models\Admin\Theme;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display wedding invitation preview
     * 
     * Route: /events/{slug}/template/{id?} or /events/{slug}
     */
    public function index(Request $request, $slug, $id = null)
    {
        $locale = $request->query('lang') === 'en' ? 'en' : 'km';
        app()->setLocale($locale);

        $event = MainCategory::where('slug', '=', $slug)->firstOrFail();
        $id    = (int) ($id ?: ($event->default_theme_id ?? 1));
        if ($id < 1) {
            $id = 1;
        }
    
        $themeMeta = [
            1 => ['name' => 'Royal Gold & Ruby Silk', 'description' => 'រាជរដ្ឋសិរីមង្គល - Traditional Royal Gold Theme'],
            2 => ['name' => 'Heritage Lotus & Ivory Silk', 'description' => 'កេរដំណែលផ្កាឈូកអង្គរ - Sacred Lotus Theme'],
            3 => ['name' => 'Modern Luxury Khmer Gold Fusion', 'description' => 'ខ្មែរបុរាណទាន់សម័យ - Modern Luxury Theme'],
        ];

        $theme = Theme::find($id);

        if (! $theme) {
            try {
                $theme = Theme::firstOrCreate(
                    ['id' => (int) $id],
                    [
                        'name'          => $themeMeta[$id]['name'] ?? ('Theme ' . $id),
                        'description'   => $themeMeta[$id]['description'] ?? ('Template ' . $id),
                        'is_free'       => true,
                        'is_active'     => true,
                        'display_order' => (int) $id,
                    ]
                );
            } catch (\Throwable $e) {
                $theme = new Theme([
                    'name'          => $themeMeta[$id]['name'] ?? ('Theme ' . $id),
                    'description'   => $themeMeta[$id]['description'] ?? ('Template ' . $id),
                    'is_free'       => true,
                    'is_active'     => true,
                    'display_order' => (int) $id,
                ]);
                $theme->id = (int) $id;
            }
        }
        $guest = $request->query('gid')
            ? Guest::find($request->query('gid'))
            : null;

        $music = Configuration::where('slug', '=', 'music')->value('value');
        $wishes = Guest::where('main_category_id', '=', $event->id)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->get();
    
        // If guest exists, move their wish to index position 1 (second)
        if ($guest) {
            $guestWish = $wishes->firstWhere('id', $guest->id);
    
            if ($guestWish) {
                // Remove guest's wish from current position
                $filtered = $wishes->reject(fn($w) => $w->id === $guest->id)->values();
    
                // Insert at index 1 (second position), or at start if only one item
                $wishes = $filtered->count() >= 1
                    ? $filtered->slice(0, 1)
                        ->push($guestWish)
                        ->concat($filtered->slice(1))
                    : collect([$guestWish]);
            }
        }
    
        return view('welcome', compact(
            'event',
            'guest',
            'theme',
            'music',
            'wishes',
            'locale'
        ));
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
     * Return donation status for a guest.
     * Route: GET /events/{slug}/guest/{gid}/donation
     */
    public function getDonationStatus($slug, $gid)
    {
        $guest    = Guest::with('donation')->findOrFail($gid);
        $donation = $guest->donation;

        return response()->json([
            'has_donation' => (bool) $donation,
            'donation'     => $donation ? [
                'payment_method' => $donation->payment_method,
                'cash_method'    => $donation->cash_method,
                'amount_usd'     => $donation->amount_usd,
                'amount_khr'     => $donation->amount_khr,
                'note'           => $donation->note,
            ] : null,
        ]);
    }

    /**
     * Create or update a guest's donation.
     * Route: POST /events/{slug}/guest/{gid}/donation
     */
    public function saveDonation(Request $request, $slug, $gid)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qr_code,other',
            'cash_method'    => 'required|in:usd,khr,both',
            'amount_usd'     => 'nullable|numeric|min:0',
            'amount_khr'     => 'nullable|numeric|min:0',
            'note'           => 'nullable|string|max:500',
        ]);

        $guest = Guest::findOrFail($gid);

        Donation::updateOrCreate(
            ['guest_id' => $guest->id],
            [
                'main_category_id' => $guest->main_category_id,
                'payment_method'   => $request->input('payment_method'),
                'cash_method'      => $request->input('cash_method'),
                'amount_usd'       => $request->input('amount_usd', 0),
                'amount_khr'       => $request->input('amount_khr', 0),
                'note'             => $request->input('note'),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Save RSVP reply and return personalised message
     * Route: POST /events/{slug}/guest/{gid}/rsvp
     */
    public function rsvpReply(Request $request, $slug, $gid)
    {
        $locale = $request->query('lang') === 'en' ? 'en' : 'km';
        $request->validate(['status' => 'required|in:yes,no']);

        $guest = Guest::findOrFail($gid);
        $isAttending = $request->input('status');
        $guest->update(['is_attending' => $isAttending]);

        // Personalised message using the saved value
        $message = ($locale === 'en')
            ? ($guest->is_attending == 'yes'
                ? '🎉 Thank you ' . $guest->name . '! We are so happy you can join us.'
                : '💛 We understand ' . $guest->name . '. Thank you for letting us know.')
            : ($guest->is_attending == 'yes'
                ? '🎉 អរគុណ ' . $guest->name . '! យើងពិតជារីករាយណាស់ដែលអ្នកអាចចូលរួមជាមួយយើងបាន។'
                : '💛 យើងយល់ហើយ ' . $guest->name . '។ សូមអរគុណសម្រាប់ការប្រាប់យើងឱ្យដឹង។');

        return response()->json([
            'success'      => true,
            'is_attending' => $guest->is_attending,
            'message'      => $message,
        ]);
    }
}
