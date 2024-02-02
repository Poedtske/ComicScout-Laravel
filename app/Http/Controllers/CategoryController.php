<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryFormRequest;


class CategoryController extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('categories.create');
        // Category::create([
        //     'name'=>$request->input('name')
        // ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryFormRequest $request)
    {
        $validated=$request->validated();
        $category=$request->user()->categories()->create($validated);


        return redirect()
                ->route('categories.show',[$category])
                ->with('success', 'Category is submitted! Name: '.
                $category->name);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('categories.show',['category'=>$category]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $this->authorize('update',$category);
        return view('categories.edit',['category'=>$category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryFormRequest $request, Category $category)
    {
        $this->authorize('update',$category);

        $validated=$request->validated();
        $category->update($validated);


        return redirect()
        //  ->route('categories.show',['post'=>$category->id]) done by laravel (route model binding)
            ->route('categories.show',[$category])//id gets extraced from $category and used
            ->with('success','Category is Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete',$category);
        $category->delete();

        return redirect()
        ->route('home2')
        ->with('success','Category has been deleted!');
    }
}
