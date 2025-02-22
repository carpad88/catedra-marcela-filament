<x-filament-panels::page>
    @php
        $projects = $this->table->getRecords();
    @endphp

    @if($projects->count())
        <div class="mt-5 grid grid-cols-2 gap-20">
            @foreach($projects as $project)
                <a href="{{ \App\Filament\App\Resources\ProjectResource::getUrl('view', ['record' => $project->id]) }}"
                   class="group text-sm hover:text-red-600 {{ $loop->index % 2 == 0 ? '' : 'pt-32' }}"
                >
                    <div class="p-4 transition group-hover:-translate-x-4 group-hover:-translate-y-4
                        group-hover:border-l group-hover:border-t group-hover:border-red-600
                    ">
                        @if(auth()->user()->hasRole('student'))
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-xs uppercase font-extralight">Fecha de entrega</div>
                                    <h3 class="font-medium">{{ $project->finished_at->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</h3>
                                </div>

                                <div class="flex items-center">
                                    <x-phosphor-arrow-square-out class="size-5 mr-1"/>
                                    <p>Ver proyecto</p>
                                </div>
                            </div>
                        @endif
                        <h2 class="font-display font-bold text-4xl leading-none my-12 ">{{ $project->title }}</h2>
                        <div class="h-64 bg-cover"
                             style="background-image: url('{{ Storage::url($project->cover) }}')">
                        </div>
                        <p class="mt-8 font-light text-base">
                            {{ $project->description }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="py-16 ">
            <h1 class="text-3xl font-bold text-red-600 text-center mb-24">
                Aun no hay proyectos definidos para este ciclo
            </h1>

            <div class="flex justify-center" style="height: 50vh;">
                <img src="{{ Storage::url('images/projects.svg') }}" alt="">
            </div>
        </div>
    @endif
</x-filament-panels::page>
