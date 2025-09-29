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
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Item;
use Livewire\Component;

class ListItems extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Item::query())
            ->columns([
                TextColumn::make('name')->icon(Heroicon::Inbox)->iconColor('active')->searchable(),
                TextColumn::make('sku')->searchable()->sortable(),
                TextColumn::make('price')->money('NGN')->sortable(),
                TextColumn::make('status')->badge()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                ->label('Add New')
                ->url(fn (): string => route('items.create'))
                ->openUrlInNewTab()
            ])
            ->recordActions([
                    Action::make('delete')
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(fn (Item $record) => $record->delete())
                        ->successNotification(
                            Notification::make()
                            ->title('Deleted Successfully')
                            ->success()
                        ),
                    Action::make('edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn (Item $record): string => route('items.update', $record))
                        ->openUrlInNewTab()
            ])
                ->toolbarActions([
                    BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.items.list-items');
    }
}
