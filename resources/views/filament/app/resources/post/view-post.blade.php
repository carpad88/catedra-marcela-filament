<div class="catedra pb-32 bg-white">

    @php
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $post = $this->getRecord();
    @endphp

    <div class="mb-16 border-b border-gray-300 ">
        <div class="container m-auto w-full flex flex-col justify-center items-center ">
            <div
                class="w-2/3 flex flex-col items-center justify-center border-r border-l border-gray-300 ">
                <div class="bg-gray-200 w-full flex justify-center h-64 bg-cover bg-center"
                     style="background-image: url('{{ Storage::url($post->cover) }}');">
                </div>
                <div class="w-full flex flex-col justify-center items-center flex-1 pb-8">
                    <div class="h-full my-8">
                        <h1 class="text-5xl font-bold text-red-600 text-center">{{ $post->title }}</h1>
                    </div>
                    <div class="font-light text-center">
                        <p class="text-base text-gray-900 mb-2">Por <span class="italic">{{ $post->creator->name }}</span></p>
                        <p class="text-sm text-gray-600 ">{{ $post->created_at->isoFormat('D [de] MMMM [de] YYYY') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container m-auto max-w-3xl">
        <div class="post prose max-w-none prose-h2:text-red-600">
            {{ Str::of($post->content)->markdown()->toHtmlString() }}
        </div>
    </div>
</div>
