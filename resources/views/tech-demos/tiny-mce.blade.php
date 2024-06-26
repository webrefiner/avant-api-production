<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tiny MCE Demo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-head.tinymce-config />
        <x-forms.tinymce-editor />
    </div>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6">
                {{ $tinymces->links() }}
            </div>
        </div>
    </div>

    @foreach ($tinymces as $item)
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        {!! $item->description !!}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
