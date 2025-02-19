<div class="w-full ">
    @php($group = $getRecord())

    <div class="font-bold text-primary-600 text-lg">
        {{ $group->year }}{{ $group->cycle }}
    </div>

    <h3 class="w-full font-bold text-lg leading-normal">{{ $group->title }}</h3>

    <x-filament::badge class="mt-8">
        {{ round($group->works_avg_score)  }} puntos
    </x-filament::badge>

    <div class="mt-8 space-x-4 flex justify-between text-sm text-gray-500">
        <div class="flex flex-col items-center justify-start space-y-2">
            <x-phosphor-calendar-dots-duotone class="size-6" />
            <p>{{ $group->projects_count }} {{ Str::of('proyecto')->plural($group->projects_count) }}</p>
        </div>

        <div class="flex flex-col items-center justify-start space-y-2">
            <x-phosphor-users-four-duotone class="size-6" />
            <p>{{ $group->students_count }} {{ Str::of('estudiante')->plural($group->students_count) }}</p>
        </div>

        <div class="flex flex-col items-center justify-start space-y-2">
            <x-phosphor-images-duotone class="size-6" />
            <p>{{ $group->works_count }} {{ Str::of('trabajo')->plural($group->works_count) }}</p>
        </div>
    </div>
</div>
