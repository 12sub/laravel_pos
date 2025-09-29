<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
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
use Livewire\Component;


class ListCustomers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Customer::query())
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')
                ->sortable(),
                TextColumn::make('phone')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                ->label('Add New')
                ->url(fn (): string => route('customers.create'))
                ->openUrlInNewTab()
            ])
            ->recordActions([
                Action::make('delete')
                    ->button()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn (Customer $record) => $record->delete())
                    ->successNotification(
                        Notification::make()
                        ->title('Customer Deleted Successfully')
                        ->success()
                    ),
                Action::make('edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn (Customer $record): string => route('customer.update', $record))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.customer.list-customers');
    }
}
