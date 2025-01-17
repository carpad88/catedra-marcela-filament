<?php

namespace App\Actions;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class DeleteRecord
{
    public static function handle(Model $record): void
    {
        try {
            $record->delete();
            Notification::make()
                ->title('Registro eliminado correctamente')
                ->success()
                ->send();
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                Notification::make()
                    ->title('Error al eliminar registro')
                    ->body('Este ítem no se puede eliminar porque tiene registros asociados con él.')
                    ->danger()
                    ->send();
            }
        }
    }
}
