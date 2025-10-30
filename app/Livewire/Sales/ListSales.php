<?php

namespace App\Livewire\Sales;

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
use App\Models\Sale;
use Filament\Tables\Columns\TextColumn;

class ListSales extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Sale::query()->with(['customer', 'saleItems']))
            ->columns([
                TextColumn::make('customer.name')
                ->sortable(),
                TextColumn::make('saleItems.item.name')
                ->bulleted()
                ->limitList(2)
                ->expandableLimitedList(),
                TextColumn::make('total')
                ->money('NGN')
                ->sortable(),
                TextColumn::make('discount')
                ->money('NGN')
                ->sortable(),
                TextColumn::make('paid_amount')->money('NGN'),
                
                TextColumn::make('paymentMethod.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('delete')
                    ->button()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn (Sale $record) => $record->delete())
                    ->successNotification(
                        Notification::make()
                        ->title('Sale Deleted Successfully')
                        ->success()
                    )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.sales.list-sales');
    }
}
