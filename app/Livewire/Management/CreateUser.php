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

class CreateUser extends Component implements HasActions, HasSchemas
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
                Section::make('User')
                ->description('Add a new User')
                ->icon(Heroicon::User)
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                    ->required(),
                    TextInput::make('email')
                    ->unique(ignoreRecord:true)
                    ->required(),
                    TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->length(12)
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
            ->model(User::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
        ->title('User Created')
        ->body("User has been created")
        ->send();
    }

    public function render(): View
    {
        return view('livewire.management.create-user');
    }
}
