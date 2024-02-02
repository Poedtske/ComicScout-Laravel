@extends('layout')

@section('title', 'Create new post')

@section('content')
<h1>Create a New Blog Post</h1>
<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
    @csrf

    <label>Title</label>
    <input class="@error('title') error-border @enderror" type="text" name="title" value="{{ old('title') }}">
    @error('title')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label>Description</label>
    <textarea class="@error('description') error-border @enderror" name="description">{{ old('description') }}</textarea>
    @error('description')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <div>
        <x-input-label for="cover" :value="__('cover')" />
        <x-text-input id="cover" name="cover" type="file" class="block w-full mt-1" autofocus autocomplete="cover" />


        <x-input-error class="mt-2" :messages="$errors->get('cover')" />
    </div>

    <label>Date</label>
    <input class="@error('date') error-border @enderror" name="date" type="date" value="{{ old('date') }}">
    @error('date')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label>Time</label>
    <input class="@error('time') error-border @enderror" name="time" type="time" value="{{ old('time') }}">
    @error('time')
    <div class="error">
        {{ $message }}
    </div>
    @enderror
    {{-- <div>
        <label>Cover</label>
        <input id="cover" name="cover" type="file" class="@error('cover') error-border @enderror" :value="old('cover')"/>
        @error('description')
        <div class="error">
            {{ $message }}
        </div>
        @enderror
    </div> --}}

    <button type="submit">Submit</button>
</form>
@endsection
