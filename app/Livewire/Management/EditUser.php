<?php

namespace App\Livewire\Management;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\User;

class EditUser extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public User $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User')
                ->description('The user panel for registered users')
                ->icon(Heroicon::User)
                ->columns(2)
                ->schema([
                    TextInput::make('name'),
                    TextInput::make('email')
                    ->unique(ignoreRecord:true),
                    Select::make('role')
                    ->options([
                        'cashier' => 'cashier',
                        'admin' => 'admin',
                        'other' => 'other',
                    ])
                    ->native(false)
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
        ->title('User Updated')
        ->body("The user {$this->record->name} has been updated successfully")
        ->send();
    }

    public function render(): View
    {
        return view('livewire.management.edit-user');
    }
}
