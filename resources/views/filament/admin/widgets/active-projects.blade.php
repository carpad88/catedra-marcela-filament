<div class="w-full ">
    @php($project = $getRecord())

    <h3 class="w-full font-bold text-2xl text-primary-500 leading-normal">{{ $project->title }}</h3>

    <div class="h-60 my-4">
        <img src="{{ Storage::url($project->cover) }}" alt="image" class="h-full w-full object-cover rounded-md">
    </div>

    <div class="my-8 text-gray-500">
        <div class="flex items-start space-x-2 mb-2">
            <x-phosphor-list-checks-duotone class="size-6"/>
            <p><b>{{ $project->criterias_count }}</b> Criterios de evaluación</p>
        </div>

        <div class="flex items-start space-x-2 mb-2">
            <x-phosphor-images-duotone class="size-6"/>
            <p><b>{{ $project->works_count }}</b> {{ Str::of('trabajo')->plural($project->works_count) }}</p>
        </div>
    </div>

    @if(count($project->groups))
        <div>
            <h4 class="text-lg font-bold mb-3">Grupos</h4>

            <ul class="text-gray-500 text-sm">
                @foreach($project->groups as $group)
                    <li class="flex items-start space-x-2 mb-2">
                        <x-phosphor-chalkboard-teacher-duotone class="size-6"/>
                        <p>{{ $group->title }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
