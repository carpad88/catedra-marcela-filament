<div class="catedra">
    @php
        setlocale(LC_ALL, 'es_ES');
        $project = $this->getRecord();
    @endphp

    <div class="py-24 bg-white border-b border-gray-300">
        <h1 class="font-display font-bold text-5xl text-center text-red-600">
            {{ $project->title }}
        </h1>
        <p class="text-center mt-2 text-sm text-gray-600 font-light">
            Fecha de entrega
            <span class="font-semibold">
                {{ $project->finished_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
            </span>
        </p>
    </div>

    <div class="bg-gray-100">
        <div class="mx-auto max-w-6xl pb-24">
            <div class="mb-8 py-24 px-12 flex items-center transform -translate-x-12">
                <div class="w-5/12 transform translate-x-24 bg-blue-200 h-64 bg-cover"
                     style="background-image: url('{{ asset($project->cover) }}')"></div>
                <div class="w-7/12 bg-white py-32 pl-32 pr-8">
                    {{ $project->description }}
                </div>
            </div>

            <div class="grid grid-cols-5 gap-12">
                <div class="md-body col-span-3">
                    <div class="md-body">
                        <h3 class="!mt-0">Objetivos</h3>
                        {{ Str::of($project->goals)->markdown()->toHtmlString() }}
                    </div>
                    <div class="md-body">
                        <h3>Actividades</h3>
                        {{ Str::of($project->activities)->markdown()->toHtmlString() }}

                    </div>
                    <div class="md-body">
                        <h3>Condiciones de entrega</h3>
                        {{ Str::of($project->conditions)->markdown()->toHtmlString() }}
                    </div>
                </div>
                <div class="col-span-2">
                    <!-- TODO: get works that shared common categories -->
                    @foreach($project->works()->randomPublic()->get() as $work)
                        @if($work->cover)
                            <img src="{{ asset($work->cover ) }}" alt=""
                                 class="w-full mb-8">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-300 py-20">
        <div class="mx-auto max-w-7xl">
            <div class="pb-16">
                <h2 class="text-3xl font-bold text-red-600 text-center">
                    Ejemplos de proyectos terminados
                </h2>
            </div>

            <div class="grid grid-cols-3">
                <!-- TODO: get works that shared common categories -->
                @foreach($project->works()->randomPublic()->get() as $work)
                    <x-work-card :$work />
                @endforeach
            </div>
        </div>
    </div>
</div>
