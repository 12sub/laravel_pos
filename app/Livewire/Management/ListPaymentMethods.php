<?php

namespace App\Livewire\Management;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Livewire\Component;
use App\Models\PaymentMethod;
use Filament\Tables\Columns\TextColumn;

class ListPaymentMethods extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => PaymentMethod::query())
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('description')
                ->limit(30)
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                ->label('Add New')
                ->url(fn (): string => route('payment.method.create'))
                ->openUrlInNewTab()
            ])
            ->recordActions([
                Action::make('delete')
                        ->button()
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(fn (PaymentMethod $record) => $record->delete())
                        ->successNotification(
                            Notification::make()
                            ->title('Payment Method Deleted Successfully')
                            ->success()
                        ),
                Action::make('edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn (PaymentMethod $record): string => route('payment.method.update', $record))
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
        return view('livewire.management.list-payment-methods');
    }
}
