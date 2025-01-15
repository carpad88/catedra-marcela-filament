<?php

namespace App\Actions;

use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class BulkDeleteRecords
{
    public static function handle(Collection $records): void
    {
        $errors = [];

        $records->each(function ($record) use (&$errors) {
            try {
                $record->delete();
            } catch (\Exception $e) {
                if ($e->getCode() === '23000') {
                    $errors[] = $record->id;
                }
            }
        });

        if (empty($errors)) {
            Notification::make()
                ->title('Registros eliminados correctamente')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Error al eliminar algunos registros')
                ->body('Algunos ítems no se pueden eliminar porque tienen registros asociados con ellos.')
                ->danger()
                ->send();
        }
    }
}
