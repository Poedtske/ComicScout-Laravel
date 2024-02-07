<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Serie;
use App\Models\Scanlator;
use Illuminate\Http\Request;

class SerieController extends Controller
{

    public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Serie $serie)
    {
        // $serie = Serie::find($serie);
        return view('series.show',['serie'=>$serie]);
    }
    public function bookmark(Serie $serie,User $user)
    {
        // Check if the serie is already bookmarked by the user
        $isBookmarked = $user->bookmarks()->where('serie_id', $serie->id)->exists();

        if ($isBookmarked) {
            // If the serie is already bookmarked, remove it
            $user->bookmarks()->detach($serie);
        } else {
            // If the serie is not bookmarked, add it
            $user->bookmarks()->attach($serie);
        }

        // Save the changes
        $user->save();

        return redirect()->route('serie.show',["serie"=>$serie]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
