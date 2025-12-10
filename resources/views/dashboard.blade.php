<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800">
            Dashboard
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">Card 1</div>
        <div class="bg-white p-4 rounded shadow">Card 2</div>
        <div class="bg-white p-4 rounded shadow">Card 3</div>
        <div class="bg-white p-4 rounded shadow">Card 4</div>
    </div>
</x-app-layout>
