<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\PaymentMethod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Inventory;
use Filament\Notifications\Notification;

class POS extends Component
{
    // properties
    public $items;
    public $customers;
    public $paymentMethods;
    public $search = '';
    public $cart = [];

    //properties for checkout
    public $customer_id = null;
    public $payment_method_id = null;
    public $paid_amount = 0;
    public $discount_amount = 0;



    public function mount() {
        // load all the items
        // $this->items = Item::with(['inventory' => function($builder) {
        //     $builder->where('quantity', '>', 0); 
        // }])
                    // ->where('status', 'active')
                    // ->get();

        $this->items = Item::whereHas('inventory', function($builder) {
            $builder->where('quantity', '>', 0);
        }) 
            ->with('inventory')
            ->where('status', 'active')
            ->get();

        // load customers
        $this->customers = Customer::all();

        // load payment method
        $this->paymentMethods = PaymentMethod::all();

        // dd($this->items, $this->customers, $this->paymentMethods);
    }

    #[Computed]
    public function filteredItems() {
        if (empty($this->search)) {
            return $this->items;
        }

        return $this->items->filter(function($item){
            return str_contains(strtolower($item->name), strtolower($this->search))
            || str_contains(strtolower($item->sku), strtolower($this->search));
        });
    }

    #[Computed]
    public function subtotal() {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    //placeholder for tax
    #[Computed]
    public function tax() {
        return $this->subtotal * 0.15;  // 15%
    }

    #[Computed]
    public function totalBeforeDiscount() {
        return $this->subtotal + $this->tax;
    }

    #[Computed]
    public function total() {
        $discountedTotal = $this->totalBeforeDiscount - $this->discount_amount;
        return $discountedTotal;
    }

    #[Computed]
    public function change() {
        if($this->paid_amount > $this->total) {
            return $this->paid_amount - $this->total;
        }
        return 0;
    }

    public function addToCart($itemId) {
        // access item from db to get inventory amount
        $item = Item::find($itemId);

        // inventory
        $inventory = Inventory::where('item_id', $itemId)->first();
        if (!$inventory || $inventory->quantity <= 0) {
            Notification::make()
                ->title('This Item is out of stock!')
                ->danger()
                ->send();
            return;
        }
        
        if (isset($this->cart[$itemId])) {
            $currentQuantity = $this->cart[$itemId]['quantity'];
            if($currentQuantity >= $inventory->quantity) {
                Notification::make()
                    ->title("You cannot add more than a certain amount! Only {$inventory->quantity} in stock")
                    ->danger()
                    ->send();
                return;
            }
        $this->cart[$itemId]['quantity']++;
        }
        // add more items
        else {
            $this->cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => $item->price,
                'quantity' => 1,
            ];
        }
    }

    // remove items from cart
    public function removeFromCart($itemId) {
        unset($this->cart[$itemId]);
    }

    // updating quantity on the cart for that item
    public function updateQuantity($itemId,$quantity) {
        //ensure quantity of an item is not less than 1
        $quantity = max(1, (int) $quantity);

        $inventory = Inventory::where('item_id', $itemId)->first();
        if ($quantity > $inventory->quantity) {
            Notification::make()
                    ->title("You cannot add more than a certain amount! Only {$inventory->quantity} in stock")
                    ->danger()
                    ->send();
            $this->cart[$itemId]['quantity'] = $inventory->quantity;
        } else {
            $this->cart[$itemId]['quantity'] = $quantity;
        }
    }

    //checkout 
    public function checkout() {
        // check if cart isnt empty
        if(empty($this->cart)) {
            Notification::make()
            ->title("Failed Sale!")
            ->body('Your cart is empty!')
            ->danger()
            ->send();
            return;
        }

        // basic validation for paid amount
        if ($this->paid_amount < $this->total) {
            Notification::make()
            ->title("U dey try commit fraud!")
            ->body('Pay up! Your payment is not complete')
            ->danger()
            ->send();
            return;
        }

        // create the sale
        // db transaction
        try{
        DB::beginTransaction();

        // create a sale
        $sale = Sale::create([
            'total' => $this->total,
            'paid_amount' => $this->paid_amount,
            'customer_id' => $this->customer_id,
            'payment_method_id' => $this->payment_method_id,
            'discount' => $this->discount_amount,
        ]);

        // create the sale items
        foreach($this->cart as $item) {
            SalesItem::create([
                'sale_id' => $sale->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // update the stock
            $inventory = Inventory::where('item_id', $item['id'])->first();
            if($inventory) {
                $inventory->quantity -= $item['quantity'];
                $inventory->save();
            }
        }

        DB::commit();

        // reset cart
        $this->cart = [];

        //reset other properties
        $this->search = '';
        $this->customer_id = null;
        $this->payment_method_id = null;
        $this->paid_amount = 0;
        $this->discount_amount = 0;

        Notification::make()
        ->title('Sale Successful')
        ->body('Sale was made successfully')
        ->success()
        ->send();
        } catch (\Exception $th){
            DB::rollback();
            Notification::make()
            ->title('Failed Sale!')
            ->body('Transaction failed to complete. Took too long!')
            ->danger()
            ->send();
        }
    }

    public function render()
    {
        return view('livewire.p-o-s');
    }
}
