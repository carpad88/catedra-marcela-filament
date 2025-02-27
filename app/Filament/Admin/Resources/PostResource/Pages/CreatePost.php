<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['content'] = '';

        return parent::mutateFormDataBeforeCreate($data);
    }

    public function afterCreate(): void
    {
        $post = $this->getRecord();

        if ($post->cover) {
            $imagePath = "posts/{$post->id}/".basename($post->cover);
            \Storage::disk('public')->move($post->cover, $imagePath);
            $post->update(['cover' => $imagePath]);
        }
    }
}
