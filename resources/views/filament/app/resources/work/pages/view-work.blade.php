<div class="catedra bg-white">
    <div class="mx-auto max-w-6xl pt-16">
        @php
            $work = $this->getRecord();
            $similarProjects = \App\Models\Work::randomPublic(3, $work->project->category_id)->get();
        @endphp

        <div>
            <div class="bg-cover bg-center {{ !$work->cover ? 'bg-gray-200' : ''}}"
                 style="height: 25rem; background-image: url('{{ Storage::url($work->cover) }}')">
            </div>

            <div class="w-1/2 h-full bg-white p-8 pb-20 transform -translate-y-32">
                <div class="mb-12">
                    <div class="text-base text-gray-600 font-light italic mb-2">
                        {{ $work->group->period }} {{ $work->project->title }}
                    </div>
                    <h1 class="text-3xl font-bold text-red-600">{{ $work->user->name }}</h1>
                </div>

                <div class="leading-relaxed text-gray-800 text-base font-light">
                    {{ $work->project->description }}
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 -translate-y-32">
                @foreach($work->images as $image)
                    <img src="{{ Storage::url($image) }}" alt="Image" class="object-cover h-64 w-full">
                @endforeach
            </div>

        </div>
    </div>

    <div class="bg-white border-t border-gray-300 py-20">
        <div class="mx-auto max-w-7xl">
            <div class="pb-16">
                <h2 class="text-3xl font-bold text-red-600 text-center">
                    Proyectos similares
                </h2>
            </div>

            <div class="grid grid-cols-3">
                @foreach($similarProjects as $work)
                    <x-work-card :$work/>
                @endforeach
            </div>
        </div>
    </div>
</div>
