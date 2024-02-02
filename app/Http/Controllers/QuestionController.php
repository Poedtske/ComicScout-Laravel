<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Http\Requests\QuestionFormRequest;
use App\Models\Category;

class QuestionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories=Category::all();
        return view('questions.create',['categories'=>$categories]);
        // Question::create([
        //     'name'=>$request->input('name')
        // ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionFormRequest $request)
    {
        $validated=$request->validated();
        $category=Category::findOrFail($request->input('category_id'));

        $question=$request->user()->questions()->create($validated)->category()->associate($category);

        $question->save();


        return redirect()
                ->route('questions.show',[$question])
                ->with('success', 'Question is submitted! Title: '.
                $question->title);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        return view('questions.show',['question'=>$question]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $this->authorize('update',$question);
        $categories=Category::all();
        return view('questions.edit',['question'=>$question,'categories'=>$categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionFormRequest $request, Question $question)
    {
        $this->authorize('update',$question);

        $validated=$request->validated();

        $category=Category::findOrFail($request->input('category_id'));
        $question->category()->associate($category)->update($validated);
        // $question->category()->associate($category);
        $question->save();


        return redirect()
        //  ->route('questions.show',['post'=>$question->id]) done by laravel (route model binding)
            ->route('questions.show',[$question])//id gets extraced from $question and used
            ->with('success','Question is Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $this->authorize('delete',$question);
        $question->delete();

        return redirect()
        ->route('home2')
        ->with('success','Question has been deleted!');
    }
}
