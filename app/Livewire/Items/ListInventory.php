<?php

namespace App\Livewire\Items;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Inventory;
use Livewire\Component;

class ListInventory extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Inventory::query())
            ->columns([
                TextColumn::make('item.name')->searchable()->sortable(),
                TextColumn::make('quantity')->sortable(),
                TextColumn::make('created_at')->badge()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                ->label('Add New')
                ->url(fn (): string => route('inventory.create'))
                ->openUrlInNewTab()
            ])
            ->recordActions([
                Action::make('delete')
                        ->button()
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(fn (Inventory $record) => $record->delete())
                        ->successNotification(
                            Notification::make()
                            ->title('Deleted Successfully')
                            ->success()
                        ),
                Action::make('edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn (Inventory $record): string => route('inventory.update', $record))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.items.list-inventory');
    }
}
