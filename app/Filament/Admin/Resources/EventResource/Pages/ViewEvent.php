<?php

namespace App\Filament\Admin\Resources\EventResource\Pages;

use App\Filament\Admin\Resources\EventResource;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ViewEvent extends Page
{
    protected static string $resource = EventResource::class;

    protected static string $view = 'filament.admin.resources.event-resource.pages.view-event';

    public $record;

    public function mount(int | string $record): void
    {
        $this->record = Event::findOrFail($record);

        static::authorizeResourceAccess();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('join')
                ->label('Join Event')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->action(function () {
                    $this->record->participants()->attach(auth()->id(), [
                        'status' => 'pending',
                        'role' => 'player',
                    ]);
                })
                ->requiresConfirmation()
                ->modalHeading('Join Event')
                ->modalDescription('Do you want to join this event? Your request will be pending approval by the event organizer.')
                ->successNotificationTitle('Join request sent')
                ->visible(fn () =>
                    !$this->record->isParticipant(auth()->user()) &&
                    !$this->record->isFull()
                ),
            Actions\Action::make('leave')
                ->label('Leave Event')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->action(fn () => $this->record->participants()->detach(auth()->id()))
                ->requiresConfirmation()
                ->modalHeading('Leave Event')
                ->modalDescription('Are you sure you want to leave this event?')
                ->successNotificationTitle('You have left the event')
                ->visible(fn () =>
                    $this->record->isParticipant(auth()->user()) &&
                    auth()->id() !== $this->record->organizer_id
                ),
            Actions\EditAction::make()
                ->visible(fn () =>
                    auth()->user()?->hasRole(['administrator', 'owner']) ||
                    auth()->id() === $this->record->organizer_id
                ),
        ];
    }
}
