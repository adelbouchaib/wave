<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Ad; // Make sure you import your Eloquent model


class AdController extends Controller
{
    public function create($id)
    {
        // Fetch or process information based on the ID
        $ad = Ad::findOrFail($id); // Replace YourModel with the relevant model

        // Pass the info to a view
        return view('theme::pages.ads.create', compact('ad'));
    }
}
