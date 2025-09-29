<div>
    <form wire:submit="save">
        {{ $this->form }}

        <br />
        <x-filament::button type="submit" color="success" icon="heroicon-m-arrow-down-on-square" icon-position="after">
            Submit
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
