<div class="catedra bg-white">
    <div class="py-20  ">
        <h1 class="font-display font-bold text-5xl text-center text-red-600 leading-none">
            {{ $this->getRecord()->name }}
        </h1>
    </div>

    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-manager="array_key_first($relationManagers)"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >

        </x-filament-panels::resources.relation-managers>
    @endif
</div>
