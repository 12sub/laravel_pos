<?php

namespace App\Livewire\Management;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;

class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => User::query())
            ->columns([
                TextColumn::make('user')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')
                ->searchable(),
                TextColumn::make('role')
                ->searchable()
                ->badge()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                ->label('Add New User')
                ->url(fn (): string => route('users.create'))
                ->openUrlInNewTab()
            ])
            ->recordActions([
                Action::make('delete')
                    ->button()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn (User $record) => $record->delete())
                    ->successNotification(
                        Notification::make()
                        ->title('User Deleted Successfully')
                        ->success()
                    ),
                    Action::make('edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (User $record): string => route('users.edit', $record))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.management.list-users');
    }
}
