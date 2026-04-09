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
        return view('welcome', compact('event', 'guest', 'theme', 'music'));
    }

}