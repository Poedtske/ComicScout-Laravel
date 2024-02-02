<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Http\Requests\PostFormRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class PostController extends Controller
{
     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostFormRequest $request)
    {
        $validated=$request->validated();
        // $this->authorize('store',$request);

        $post=$request->user()->posts()->create($validated);

        if($request->file('cover')!=null){
            $postCoverUploaded= $request->file('cover');
        $postCoverName=$post->id.'.'.$request->cover->extension();
        $postCoverPath=public_path(('\\images\\covers\\'));


        $post->cover='/images/covers/'.$postCoverName;
        $post->save();
        $postCoverUploaded->move($postCoverPath,$postCoverName);
        }
        //validated returns something similar as below
        //$post=Post::create($validated);
        //this is done by create, Mass Assignment
        //$post=new Post();
        //$post->title=$request->input('title');
        //$post->description=$request->input('description');
        //$post->save();

        return redirect()
                ->route('posts.show',[$post])
                ->with('success', 'Post is submitted! Title: '.
                $post->title.' Description: '.
                $post->description);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

        return view('posts.show',['post'=>$post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update',$post);
        return view('posts.edit',['post'=>$post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostFormRequest $request, Post $post)
    {
        //cover($request);

        $this->authorize('update',$post);

        $validated=$request->validated();
        $post->update($validated);
        if($request->file('cover')!=null){
            $postCoverUploaded= $request->file('cover');
            $postCoverName=$post->id.'.'.$request->cover->extension();
            $postCoverPath=public_path(('\\images\\covers\\'));
            if($postCoverPath.$postCoverName!=null){
                File::delete($postCoverPath.$postCoverName);
            }

        $post->cover='/images/covers/'.$postCoverName;
        $post->save();
        $postCoverUploaded->move($postCoverPath,$postCoverName);
        }
        //$post=Post::findOrFail($id); is called by post itself
        //// $post->update($validated);
        // $post->title=$request->input('title');
        // $post->description=$request->input('description');
        // $post->save();

        return redirect()
        //  ->route('posts.show',['post'=>$post->id]) done by laravel (route model binding)
            ->route('posts.show',[$post])//id gets extraced from $post and used
            ->with('success','Post is Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete',$post);
        File::delete(public_path(($post->cover)));
        $post->delete();

        return redirect()
        ->route('home2')
        ->with('success','Post has been deleted!');
    }
}
