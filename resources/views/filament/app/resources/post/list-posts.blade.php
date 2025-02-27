<div class="w-full catedra">

    @php
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $posts = $this->table->getRecords();
        $paginator = $this->table->paginated();
    @endphp

    <div class="pt-20 pb-24 bg-white border-b border-gray-300">
        <h1 class="font-display font-bold text-5xl text-center text-red-600 leading-none">
            Apuntes
        </h1>
    </div>

    <div>
        @foreach($posts as $post)
            <a href="{{ \App\Filament\App\Resources\PostResource::getUrl('view', ['record' => $post->id]) }}"
               class="flex border-b border-gray-300 group"
               style="min-height: 50vh;">
                <div class="w-1/3 flex-none flex items-center relative border-r border-gray-300">
                    <div class="w-2/3 h-full"
                         style="background-image: url('{{ Storage::url($post->cover) }}');
                             background-position: center;
                             background-size: cover; filter: grayscale(100%) opacity(60%)">
                    </div>
                    <div class="w-2/3 h-64 absolute right-0 transition transform ease-in-out
                                duration-300 group-hover:text-red-600 group-hover:translate-x-20"
                         style="background-image: url('{{ Storage::url($post->cover) }}');
                             background-position: center;
                             background-size: cover;">
                    </div>
                </div>

                <div class="transition ease-in-out duration-300 group-hover:bg-red-600 w-full">
                    <div class="p-24 w-9/12 transform transition ease-in-out duration-300
                                group-hover:translate-x-8">
                        <div class="text-gray-600 font-light group-hover:text-white">
                            {{  $post->created_at->isoFormat('D [de] MMMM [de] YYYY') }} | Por <span
                                class="italic">{{  $post->creator->name }}</span>
                        </div>
                        <h2 class="font-bold text-4xl mt-4 text-red-600 group-hover:text-black">
                            {{ $post->title }}
                        </h2>
                        <p class="mt-12 mb-6 text-gray-600 text-lg font-light group-hover:text-white">
                            {{ $post->excerpt }}
                        </p>
                        <div class="text-red-600 group-hover:text-black flex space-x-2">
                            <p>Leer</p>
                            <x-phosphor-arrow-right-duotone class="size-6"/>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>


    <div class="my-16 mx-auto max-w-5xl">
        <x-filament::pagination
            :paginator="$posts"
            :page-options="[5, 10, 15]"
            class="posts-pagination"
        />
    </div>
</div>
