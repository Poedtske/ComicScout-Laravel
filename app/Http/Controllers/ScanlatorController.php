<?php

namespace App\Http\Controllers;

use App\Models\Serie;
use App\Models\Scanlator;
use Illuminate\Http\Request;

class ScanlatorController extends Controller
{
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
    // public function create()
    // {
    //     return view('scanlators.create');
    //     // Scanlator::create([
    //     //     'name'=>$request->input('name')
    //     // ]);

    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(ScanlatorFormRequest $request)
    // {
    //     $validated=$request->validated();

    //     $scanlator=$request->user()->scanlators()->create($validated);


    //     return redirect()
    //             ->route('scanlators.show',[$scanlator])
    //             ->with('success', 'Scanlator is submitted! Title: '.
    //             $scanlator->title);
    // }

    /**
     * Display the specified resource.
     */
    public function show(Scanlator $scanlator)
    {
        return view('scanlator.show',['scanlator'=>$scanlator]);
    }
    public function showserie(Serie $serie)
    {
        return view('scanlator.showSerie',['serie'=>$serie]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Scanlator $scanlator)
    // {
    //     $this->authorize('update',$scanlator);
    //     return view('scanlators.edit',['scanlator'=>$scanlator]);
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(ScanlatorFormRequest $request, Scanlator $scanlator)
    // {
    //     $this->authorize('update',$scanlator);

    //     $validated=$request->validated();

    //     $scanlator->update($validated);
    //     // $scanlator->category()->associate($category);
    //     $scanlator->save();


    //     return redirect()
    //     //  ->route('scanlators.show',['post'=>$scanlator->id]) done by laravel (route model binding)
    //         ->route('scanlators.show',[$scanlator])//id gets extraced from $scanlator and used
    //         ->with('success','Scanlator is Updated!');
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Scanlator $scanlator)
    // {
    //     $this->authorize('delete',$scanlator);
    //     $scanlator->delete();

    //     return redirect()
    //     ->route('home2')
    //     ->with('success','Scanlator has been deleted!');
    // }
}
