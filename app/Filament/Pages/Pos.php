<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Sale;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel\Concerns\HasNotifications;
use Illuminate\Support\Collection as EloquentCollection;
use Livewire\Attributes\On;
use App\Services\CartService;


class Pos extends Page
{
    use HasNotifications;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $title          = 'Kassa (POS)';
    protected static string  $view           = 'filament.pages.pos';

    public string $search = '';

    /** @var EloquentCollection<int, \App\Models\Product> */
    public EloquentCollection $products;

    public array $cart   = [];
    public array $totals = ['qty' => 0, 'amount' => 0];

    public function mount(): void
    {
        $this->products = new EloquentCollection();
        $this->refreshCart();
    }

    /* ---------- Qidiruv ---------- */
    public function updatedSearch(): void
    {
        $this->products = Product::query()
            ->where(fn ($q) =>
            $q->where('barcode', 'ILIKE', "%{$this->search}%")
                ->orWhere('name',   'ILIKE', "%{$this->search}%")
            )
            ->orderBy('name')
            ->limit(15)
            ->get();
    }

    /* ---------- Savat operatsiyalari ---------- */
    public function add(int $id): void
    {
        app(CartService::class)->add(Product::findOrFail($id));
        $this->refreshCart();
    }

    public function updateQty(int $id, int $qty): void
    {
        app(CartService::class)->update($id, $qty);
        $this->refreshCart();
    }

    public function remove(int $id): void
    {
        app(CartService::class)->remove($id);
        $this->refreshCart();
    }

    /* ---------- Chekout ---------- */
    public function checkout(): void
    {
        $cartService = app(CartService::class);
        $totals      = $cartService->totals();

        if (!$totals['qty']) {
            Notification::make()
                ->title('Savat bo`sh')
                ->warning()
                ->send();
            return;
        }

        \DB::transaction(function () use ($cartService, $totals) {
            $sale = \App\Models\Sale::create(['total' => $totals['amount']]);

            foreach ($cartService->all() as $row) {
                $sale->items()->create([
                    'product_id' => $row['id'],
                    'qty'        => $row['qty'],
                    'price'      => $row['price'],
                    'subtotal'   => $row['qty'] * $row['price'],
                ]);

                // Product::whereKey($row['id'])->decrement('stock', $row['qty']); // ixtiyoriy
            }
        });

        $cartService->clear();
        $this->reset('search');
        $this->products = new EloquentCollection();
        $this->refreshCart();
        Notification::make()
            ->title('Sotuv yopildi')
            ->success()
            ->send();
    }

    /* ---------- Helper ---------- */
    #[On('refresh-cart')]
    public function refreshCart(): void
    {
        $cartService   = app(CartService::class);
        $this->cart    = $cartService->all();
        $this->totals  = $cartService->totals();
    }

    /* ---------- Skaner Enter hodisasi ---------- */
    public function scanEnter(): void
    {
        $code = trim($this->search);
        if (!$code) return;

        $product = Product::where('barcode', $code)->first();
        if ($product) {
            $this->add($product->id);
            $this->reset('search');
        } else {
            Notification::make()
                ->title('Mahsulot topilmadi')
                ->danger()
                ->send();
        }
    }

    public function addByBarcode(string $value): void
    {
        $value = trim($value);
        if (!$value) {
            return;
        }

        $product = Product::where('barcode', $value)
            ->orWhere(function ($q) use ($value) {
                $q->where('name', 'ILIKE', "{$value}%")
                ->orWhere('name', 'ILIKE', "%{$value}");
            })
            ->first();

        if ($product) {
            app(CartService::class)->add($product);
            $this->reset('search');          // inputni bo‘shatish
            $this->refreshCart();
            Notification::make()
                ->title('Savatga qo‘shildi')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Mahsulot topilmadi')
                ->danger()
                ->send();
        }
    }

}

