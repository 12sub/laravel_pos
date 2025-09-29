<?php

namespace App\Livewire\Items;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use App\Models\Inventory;
use Livewire\Component;

class CreateInventory extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Create Inventory')
                    ->description('Create a new inventory to store your items')
                    ->columns(2)
                    ->schema([
                        Select::make('item_id')
                        ->relationship('item', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),
                        TextInput::make('quantity')
                            ->numeric(),
                        Toggle::make('status')
                            ->onIcon(Heroicon::Check)
                            ->offIcon(Heroicon::XMark)
                            ->onColor('success')
                ])
            ])
            ->statePath('data')
            ->model(Inventory::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Inventory::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
        ->title('Inventory Created')
        ->success()
        ->body("Inventory has been created")
        ->send();
    }

    public function render(): View
    {
        return view('livewire.items.create-inventory');
    }
}
