<?php

use App\Livewire\Customer\CreateCustomer;
use App\Livewire\Customer\EditCustomers;
use App\Livewire\Items\ListInventory;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Management\ListUsers;
use App\Livewire\Items\ListItems;
use App\Livewire\Items\CreateInventory;
use App\Livewire\Sales\ListSales;
use App\Livewire\Customer\ListCustomers;
use App\Livewire\Items\EditInventory;
use App\Livewire\Items\EditItems;
use App\Livewire\Items\CreateItem;
use App\Livewire\Management\CreatePaymentMethod;
use App\Livewire\Management\CreateUser;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Management\EditPaymentMethod;
use App\Livewire\Management\EditUser;
use App\Livewire\POS;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/manage-users', ListUsers::class)->name('users.index');
    Route::get('/edit-user/{record}', EditUser::class)->name('users.edit');
    Route::get('/create-user', CreateUser::class)->name('users.create');

    Route::get('/manage-items', ListItems::class)->name('items.index');
    Route::get('/create-items', CreateItem::class)->name('items.create');
    Route::get('/edit-item/{record}', EditItems::class)->name('items.update');

    Route::get('/manage-customers', ListCustomers::class)->name('customers.index');
    Route::get('/edit-customer/{record}', EditCustomers::class)->name('customer.update');
    Route::get('/create-customers', CreateCustomer::class)->name('customers.create');

    Route::get('/manage-sales', ListSales::class)->name('sales.index');

    Route::get('/manage-payment-methods', ListPaymentMethods::class)->name('payment.method.index');
    Route::get('/edit-payment-methods/{record}', EditPaymentMethod::class)->name('payment.method.update');
    Route::get('/create-payment-methods', CreatePaymentMethod::class)->name('payment.method.create');

    Route::get('/edit-inventory/{record}', EditInventory::class)->name('inventory.update');
    Route::get('/manage-inventories', ListInventory::class)->name('inventories.index');
    Route::get('/create-inventory', CreateInventory::class)->name('inventory.create');

    Route::get('/pos', POS::class)->name('pos');
});

require __DIR__.'/auth.php';
