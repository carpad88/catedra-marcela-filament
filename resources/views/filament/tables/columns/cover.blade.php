<div class="w-full">
    @php($work = $getRecord())

    <div class="flex mb-4 gap-x-3">
        <div
            class="size-12 flex items-center justify-center rounded-md text-xl font-medium bg-primary-300/10 text-primary-600 border border-primary-300/30">
            {{ $work->score }}
        </div>
        <div>
            <h3 class="font-bold text-lg">{{ $work->project->title }}</h3>
            <p class="text-sm text-gray-500">{{ $work->project->finished_at->format('F d, Y') }}</p>
        </div>
    </div>

    @if($getState())
        <div class="h-60">
            <img src="{{ asset($getState()) }}" alt="image" class="h-full w-full object-cover rounded-md">
        </div>
    @endif
</div>
