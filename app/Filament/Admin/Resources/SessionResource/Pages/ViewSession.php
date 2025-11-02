<?php

namespace App\Filament\Admin\Resources\SessionResource\Pages;

use App\Filament\Admin\Resources\SessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSession extends ViewRecord
{
    protected static string $resource = SessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('terminate')
                ->label('Terminate Session')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->delete();
                    $this->redirect(SessionResource::getUrl('index'));
                })
                ->successNotification(
                    fn () => \Filament\Notifications\Notification::make()
                        ->title('Session terminated successfully')
                        ->success()
                        ->send()
                ),
        ];
    }
}
