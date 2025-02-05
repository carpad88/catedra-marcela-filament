@props(['work'])

<a href="{{ \App\Filament\App\Resources\WorkResource::getUrl('view', ['record' => $work->id]) }}"
   class="text-gray-600 font-light text-sm px-3 transition transform ease-in-out duration-300 hover:text-red-600 hover:-translate-y-4">
    <h3 class="mb-2">{{ $work->group->period }} | {{ $work->user->name }}</h3>

    @if($work->cover)
        <div style="background-image: url('{{ Storage::url($work->cover ) }}')"
             class="bg-cover bg-center bg-no-repeat w-full h-64 mb-4"></div>
    @else
        <div style="background-image: url('{{ asset('images/resources.svg' ) }}')"
             class="bg-contain bg-center bg-no-repeat w-full h-64 mb-4"></div>
    @endif

    <div class="flex items-center">
        <x-phosphor-arrow-square-out class="size-5 mr-1"/>
        <p class="">Ver proyecto</p>
    </div>
</a>
