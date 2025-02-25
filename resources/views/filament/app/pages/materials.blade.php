<div class="w-full catedra">
    <div class="pt-20 pb-24 bg-white">
        <h1 class="font-display font-bold text-5xl text-center text-red-600 leading-none">
            Recursos
        </h1>
        <div class="container m-auto mt-16 flex justify-center">
            <p class="text-center text-2xl font-hairline w-2/3">
                Una lista de libros, artículos, videos, aplicaciones, herramientas <br> y otros recursos relacionados
                con el diseño editorial
            </p>
        </div>
    </div>

    <div class="bg-white pb-24">
        <div class="border-b border-gray-300">
            <div class="flex justify-center">
                <ul class="flex space-x-2 font-medium">
                    @foreach($categories as $category)
                        <li class="p-4 cursor-pointer {{ $selectedCategory == $category->id ? 'text-red-600 border-b border-red-600' : '' }}"
                            wire:click="fetchItems({{ $category->id }})"
                        >
                            {{ $category->name }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="container mx-auto max-w-7xl mt-16 grid grid-cols-2 gap-12">
            @foreach($items as $item)
                @if($item->category->name === 'Libros')
                    <a href="{{ $item->data['link'] ?? '#' }}" target="{{ $item->data['link'] ? '_blank' : '_self' }}"
                       class="px-12 pl-4 border-l hover:border-red-600 hover:text-red-600 group cursor-pointer">
                        <h2 class="text-2xl font-bold mb-1">
                            {{ $item->title }}
                        </h2>

                        <div class="mb-8">
                            <div class="flex text-gray-600 text-xl space-x-2 items-center">
                                <x-phosphor-identification-card-light class="size-6 group-hover:text-red-600"/>
                                <h3 class="italic font-light group-hover:text-red-600">
                                    {{ $item->author }}
                                </h3>
                            </div>
                        </div>

                        <div class="flex space-x-6">
                            <div class="mb-4">
                                <div class="flex text-gray-600 space-x-2 items-center">
                                    <x-phosphor-book-light class="size-5 group-hover:text-red-600"/>
                                    <h3 class="italic font-light group-hover:text-red-600">
                                        {{ $item->data['year'] }}
                                    </h3>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex text-gray-600 space-x-2 items-center">
                                    <x-phosphor-globe-hemisphere-west-light class="size-5 group-hover:text-red-600"/>
                                    <h3 class="italic font-light group-hover:text-red-600">
                                        {{ $item->data['location'] }}
                                    </h3>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex text-gray-600 space-x-2 items-center">
                                    <x-phosphor-book-open-light class="size-5 group-hover:text-red-600"/>
                                    <h3 class="italic font-light group-hover:text-red-600">
                                        {{ $item->data['publisher'] }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ $item->data['link'] ?? '#' }}" target="{{ $item->data['link'] ? '_blank' : '_self' }}"
                       class="px-12 pl-4 border-l hover:border-red-600 hover:text-red-600 group cursor-pointer"
                    >
                        <div class="mb-6 hover:text-red-600 ">
                            <h2 class="text-2xl font-bold">{{ $item->title }}</h2>
                            <h3 class="italic font-light hover:text-red-600 text-sm">
                                {{ $item->author }}
                            </h3>
                        </div>

                        @if($item->tags && count($item->tags))
                            <ul class="flex text-xs text-gray-600 font-light uppercase space-x-3">
                                @foreach($item->tags as $tag)
                                    <li class="px-2 py-1 border rounded ">
                                        {{ $tag->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <p class="font-light">{{ $item->data['description'] }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
