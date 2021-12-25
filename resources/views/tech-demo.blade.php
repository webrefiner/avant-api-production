<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="flex-auto flex space-x-3">
            <a href="{{ route('tiny-mce-demo.index') }}"
                class="w-1/3 h-16 flex items-center justify-center rounded-md border border-gray-300">
                Tiny MCE
            </a>
        </div>
    </div>
</x-app-layout>
