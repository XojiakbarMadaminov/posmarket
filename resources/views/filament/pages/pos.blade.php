<x-filament::page>
    {{-- Auto-focus script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sahifa yuklanganda
            setTimeout(() => {
                const input = document.querySelector('input[placeholder*="Skanerlash"]');
                if (input) {
                    input.focus();
                }
            }, 200);
        });

        // Livewire komponentlar yangilanganda
        document.addEventListener('livewire:navigated', function() {
            setTimeout(() => {
                const input = document.querySelector('input[placeholder*="Skanerlash"]');
                if (input) {
                    input.focus();
                }
            }, 100);
        });
    </script>

    {{-- Cart Tabs - Ko'p savat tugmalari --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <h3 class="font-semibold">Faol savatlar:</h3>
            <x-filament::button wire:click="createNewCart" size="sm" color="success">
                + Yangi savat
            </x-filament::button>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($activeCarts as $cartId => $cartTotals)
                <div class="relative">
                    <x-filament::button
                        wire:click="switchCart({{ $cartId }})"
                        size="sm"
                        :color="$activeCartId === $cartId ? 'primary' : 'gray'"
                        class="relative pr-8"
                    >
                        Savat #{{ $cartId }}
                        @if($cartTotals['qty'] > 0)
                            <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                                {{ $cartTotals['qty'] }}
                            </span>
                        @endif
                    </x-filament::button>

                    @if(count($activeCarts) > 1)
                        <button
                            wire:click="closeCart({{ $cartId }})"
                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs hover:bg-red-600"
                            title="Savatni yopish"
                        >
                            ×
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-2 text-sm text-gray-600">
            Joriy savat: <strong>#{{ $activeCartId }}</strong>
            @if($totals['qty'] > 0)
                - {{ $totals['qty'] }} dona, {{ number_format($totals['amount'], 2, '.', ' ') }} so'm
            @endif
        </div>
    </div>

    {{-- Asosiy content --}}
    <div class="space-y-6 lg:grid lg:grid-cols-2 lg:gap-8 lg:space-y-0">
        <div>
            {{-- Qidiruv input --}}
            <x-filament::input
                x-data="{
                    focusInput() {
                        this.$refs.searchInput.focus();
                    }
                }"
                x-ref="searchInput"
                x-init="
                    $nextTick(() => focusInput());
                    // Page ko'rinishga kelganda ham focus qilish
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            setTimeout(() => focusInput(), 100);
                        }
                    });
                "
                x-on:keydown.enter="$wire.addByBarcode($event.target.value); $event.target.value=''; $nextTick(() => focusInput())"
{{--                x-on:blur="setTimeout(() => focusInput(), 100)"--}}
                wire:model.live="search"
                placeholder="Skanerlash yoki qo'lda kiriting..."
                autofocus
            />

            {{-- Qidiruv natijalari --}}
            @if($products->isNotEmpty())
                <table class="w-full mt-4 text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Barcode</th>
                        <th class="px-2 py-1 text-left">Nomi</th>
                        <th class="px-2 py-1 text-right">Narxi</th>
                        <th class="px-2 py-1"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $p->barcode }}</td>
                            <td class="px-2 py-1">{{ $p->name }}</td>
                            <td class="px-2 py-1 text-right">{{ number_format($p->price, 2, '.', ' ') }}</td>
                            <td class="px-2 py-1">
                                <x-filament::button wire:click="add({{ $p->id }})" size="sm">
                                    Qo'shish
                                </x-filament::button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Joriy savat --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold">Savat #{{ $activeCartId }}</h2>
                @if($totals['qty'] > 0)
                    <span class="text-sm text-gray-600">{{ $totals['qty'] }} dona</span>
                @endif
            </div>

            @if(empty($cart))
                <div class="text-gray-500">Mahsulot yo'q</div>
            @else
                <table class="w-full text-sm">
                    <thead>
                    <tr>
                        <th class="text-left">Nomi</th>
                        <th class="text-center">Miqdori</th>
                        <th class="text-right">Narx</th>
                        <th class="text-right">Jami</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cart as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-center">
                                <input type="number" min="1"
                                       x-on:change="
                                        $wire.updateQty({{ $row['id'] }}, $event.target.value);
                                        setTimeout(() => $refs.searchInput.focus(), 100);"
                                       value="{{ $row['qty'] }}"
                                       class="w-16 border rounded px-1 text-center">
                            </td>
                            <td class="text-right">{{ number_format($row['price'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['qty'] * $row['price'], 2) }}</td>
                            <td class="text-right">
                                <button wire:click="remove({{ $row['id'] }})" class="text-red-600 hover:text-red-800">×</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="border-t mt-3 pt-3 text-right space-x-6">
                    <span class="font-medium">Dona: {{ $totals['qty'] }}</span>
                    <span class="font-semibold">Summa: {{ number_format($totals['amount'], 2, '.', ' ') }} so'm</span>
                </div>

                <x-filament::button wire:click="checkout" color="success" class="mt-4 w-full">
                    Savat #{{ $activeCartId }} ni yakunlash
                </x-filament::button>
            @endif
        </div>
    </div>

    {{-- Barcha faol savatlar ko'rinishi (ixtiyoriy) --}}
    @if(count($activeCarts) > 1)
        <div class="mt-8">
            <h3 class="font-semibold mb-4">Barcha faol savatlar:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeCarts as $cartId => $cartTotals)
                    <div class="border rounded-lg p-3 {{ $activeCartId === $cartId ? 'bg-blue-50 border-blue-300' : 'bg-white' }}">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-medium">Savat #{{ $cartId }}</h4>
                            @if($activeCartId === $cartId)
                                <span class="text-blue-600 text-sm font-medium">Faol</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            <div>Mahsulotlar: {{ $cartTotals['qty'] }} dona</div>
                            <div>Summa: {{ number_format($cartTotals['amount'], 2, '.', ' ') }} so'm</div>
                        </div>
                        <div class="mt-2 flex gap-2">
                            @if($activeCartId !== $cartId)
                                <x-filament::button wire:click="switchCart({{ $cartId }})" size="xs">
                                    Ochish
                                </x-filament::button>
                            @endif
                            @if(count($activeCarts) > 1)
                                <x-filament::button
                                    wire:click="closeCart({{ $cartId }})"
                                    size="xs"
                                    color="danger"
                                    wire:confirm="Savat #{{ $cartId }} ni yopishni tasdiqlaysizmi?"
                                >
                                    Yopish
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-filament::page>
