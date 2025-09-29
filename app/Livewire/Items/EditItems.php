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
use Illuminate\Contracts\View\View;
use App\Models\Item;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;
use Tiptap\Core\Mark;

class EditItems extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Item $record;

    public ?array $data = [];

    public function mount(): void
    {
        // populate the defaults values from db
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Update Item')
                    ->description('Kindly update your Item so that we can hear word!')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Item Name'),
                        TextInput::make('sku')
                            ->unique(),
                        TextInput::make('price')
                            ->numeric(),
                        Toggle::make('status')
                            ->onIcon(Heroicon::Check)
                            ->offIcon(Heroicon::XMark)
                            ->onColor('success')
                        
                ])
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
        ->title('Item Updated')
        ->body("The item {$this->record->name} has been updated successfully")
        ->send();
    }

    public function render(): View
    {
        return view('livewire.items.edit-items');
    }
}
