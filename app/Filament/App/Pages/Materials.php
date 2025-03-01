<?php

namespace App\Filament\App\Pages;

use App\Models\Material;
use App\Models\Tag;
use Illuminate\Support\Collection;

class Materials extends \Filament\Pages\Dashboard
{
    protected static string $view = 'filament.app.pages.materials';

    protected static ?string $navigationIcon = 'phosphor-books-duotone';

    protected static ?string $title = 'Recursos';

    protected static string $routePath = '/materials';

    protected static ?int $navigationSort = 2;

    public Collection|array|null $items = [];

    public Collection|array $categories = [];

    public ?int $selectedCategory = null;

    public function mount(): void
    {
        $this->fetchCategories();
        $this->fetchItems();
    }

    public function fetchItems(?int $category = null): void
    {
        $this->selectedCategory = $category ?? $this->categories->first()->id;

        $this->items = Material::with('category')->where('category_id', $this->selectedCategory)->get();
    }

    protected function fetchCategories(): void
    {
        $this->categories = Tag::withType('Recursos')->get();
    }
}
