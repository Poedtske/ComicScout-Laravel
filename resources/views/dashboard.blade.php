<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('home2') }}"class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">

                    <button class="p-6 text-gray-900 dark:text-gray-100">
                        Home
                    </button>
            </div>
        </div>
    </a>
<br>
    <a href="{{ route('members') }}"class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">

                    <button class="p-6 text-gray-900 dark:text-gray-100">
                        UserList
                    </button>
            </div>
        </div>
    </a>

    <a href="{{ route('bookmarks') }}"class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">

                    <button class="p-6 text-gray-900 dark:text-gray-100">
                        Bookmarks
                    </button>
            </div>
        </div>
    </a>

    @auth
        @admin
        @foreach ($messages as $message)
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h1 class="p-6 text-gray-900 dark:text-gray-100">Contact message id:{{ $message->id }}</h1>
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p><b>Title:</b> {{ $message->title }}</p>
                        <p><b>Email:</b> {{ $message->email }}</p>
                        <p><b>Content:</b> {{ $message->content }}</p>
                        <form method="POST" action="{{ route('contact.destroy',[$message]) }}">
                            @csrf
                            @method('DELETE')
                            <button style="background-color: red" class="delete" type="submit">Delete post</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endadmin
    @endauth
</x-app-layout>
