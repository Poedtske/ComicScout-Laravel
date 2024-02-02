@extends('layout')
@section('title', 'Create new post')

@section('content')
    <form method="POST" action="{{ route('contact.store') }}" style="padding-left: auto;">
        @csrf

        <label>Title</label>
        <input class="@error('title') error-border @enderror" type="text" name="title" value="{{ old('title') }}">
        @error('title')
        <div class="error">
            {{ $message }}
        </div>
        @enderror
        <!-- Email Address -->
        <Label>Email</Label>
        <input class="@error('email') error-border @enderror" type="email"name="email" value={{ old('email') }}>

        <br>
        {{-- Content --}}
        <label>Content</label>
        <textarea class="@error('content') error-border @enderror" name="content">{{ old('content') }}</textarea>
        @error('content')
        <div class="error">
            {{ $message }}
        </div>
        @enderror



        <button type="submit">Submit</button>

    </form>
    @endsection
