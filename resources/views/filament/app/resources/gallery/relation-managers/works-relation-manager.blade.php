<div>
    @php
        $records = $this->table->getRecords();
    @endphp

    <div class="container mx-auto flex flex-wrap w-10/12">
        @forelse($records as $work)
            <a href="{{ \App\Filament\App\Resources\WorkResource::getUrl('view', ['record' => $work]) }}"
               class="relative group overflow-hidden w-1/2 border border-red-500">
                @if($work->cover)
                    <div
                        style="background-image: url('{{ \Storage::url($work->cover) }}'); height: 36rem;"
                        class="bg-cover bg-center bg-no-repeat">
                    </div>
                @else
                    <img src="{{ \Storage::url('images/placeholder.svg' ) }}" alt=""
                         class="w-full p-32">
                @endif
                <div
                    class="absolute bottom-0 w-full h-48 transition transform ease-in-out mt-5 duration-200 px-4 translate-y-48 group-hover:translate-y-0">
                    <div
                        class="flex flex-col justify-between h-full bg-white p-4 opacity-75">
                        <div>
                            <h2 class="text-2xl font-bold text-red-600">{{ $work->user->name }}</h2>
                            <h3 class="text-gray-600">{{ $work->group->period }}</h3>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 hover:text-red-600">
                            <x-phosphor-arrow-square-right-duotone class="size-5 mr-1"/>
                            Ver galería del proyecto
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="flex justify-center w-full">
                <h2 class="font-bold text-3xl">No hay trabajos publicados para este proyecto</h2>
            </div>
        @endforelse
    </div>

    <div class="my-16 mx-auto max-w-5xl">
        <x-filament::pagination
            :paginator="$records"
            :page-options="[]"
            class="gallery"
        />
    </div>
</div>
