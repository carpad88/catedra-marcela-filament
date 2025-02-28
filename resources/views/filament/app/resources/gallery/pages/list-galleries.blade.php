<div class="catedra bg-white">
    <div class="py-20 bg-white ">
        <h1 class="font-display font-bold text-5xl text-center text-red-600 leading-none">
            Galería de proyectos
        </h1>
    </div>

    <div class="container m-auto">
        <div class="px-8 my-12 flex flex-wrap w-full ">
            @php
                use App\Filament\App\Resources\GalleryResource;
            @endphp

            @foreach($categories as $category)
                <a href="{{ GalleryResource::getUrl('view', ['record' => $category]) }}"
                   class="relative group w-1/2 h-80 p-2 ">
                    <div
                        class="bg-white w-full h-full flex justify-end items-end border-4
                        border-red-600 bg-cover bg-no-repeat overflow-hidden"
                        style="background-image: url('{{ \Storage::url($category->projects->first()->cover) }}');">
                        <h2 class="text-3xl text-white bg-red-600
                        group-hover:font-extrabold transition transform ease-in-out duration-200
                        pl-6 pr-8 pt-4 pb-8 leading-none
                        translate-y-6 translate-x-4 group-hover:translate-x-0 group-hover:translate-y-4">
                            {{ $category->name }}
                        </h2>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
