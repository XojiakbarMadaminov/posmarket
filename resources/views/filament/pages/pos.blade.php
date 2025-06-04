<x-filament::page class="bg-gray-100 dark:bg-gray-950">
    {{-- Auto-focus script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const input = document.querySelector('input[name="Search"]');
                if (input) {
                    input.focus();
                }
            }, 200);
        });

        document.addEventListener('livewire:navigated', function() {
            setTimeout(() => {
                const input = document.querySelector('input[name="Search"]');
                if (input) {
                    input.focus();
                }
            }, 100);
        });
    </script>

    {{-- Cart Management Section --}}
    <x-filament::card class="mb-6" wire:key="cart-header-{{ $activeCartId }}-{{ $totals['qty'] }}">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3 sm:mb-0">Faol savatlar</h3>
            <x-filament::button wire:click="createNewCart" size="md" color="success" icon="heroicon-o-plus-circle">
                Yangi savat
            </x-filament::button>
        </div>

        @if(count($activeCarts) > 0)
            <div class="flex flex-wrap gap-2 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                @foreach($activeCarts as $cartId => $cartTotals)
                    <div wire:key="cart-{{ $cartId }}" class="relative group">
                        <x-filament::button
                            wire:click="switchCart({{ $cartId }})"
                            size="sm"
                            :color="$activeCartId === $cartId ? 'primary' : 'gray'"
                            :outlined="$activeCartId !== $cartId"
                            class="relative {{ count($activeCarts) > 1 ? 'pr-10' : '' }}"
                            tag="button"
                        >
                            Savat #{{ $cartId }}
                            @if($cartTotals['qty'] > 0)
                                <span class="ml-1.5 bg-danger-500 text-white text-xs rounded-full px-1.5 py-0.5 font-medium">
                                    {{ $cartTotals['qty'] }}
                                </span>
                            @endif
                        </x-filament::button>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-sm text-gray-600 dark:text-gray-400">
            Joriy savat: <strong class="text-gray-900 dark:text-white">#{{ $activeCartId }}</strong>
            @if(isset($totals['qty']) && $totals['qty'] > 0)
                <span class="mx-1 text-gray-400 dark:text-gray-600">|</span>
                {{ $totals['qty'] }} mahsulot,
                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($totals['amount'], 0, '.', ' ') }} so'm</span>
            @else
                <span class="mx-1 text-gray-400 dark:text-gray-600">|</span> Savat bo'sh
            @endif
        </div>
    </x-filament::card>

    {{-- Asosiy content --}}
    <div class="space-y-6 lg:grid lg:grid-cols-2 lg:gap-8 lg:space-y-0">
        <div>
            {{-- Qidiruv input --}}
            <x-filament::input.wrapper class="mb-4">
                <x-slot name="prefix">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                </x-slot>
                <x-filament::input
                    name="search"
                    x-data="{
                            focusInput() {
                                this.$refs.searchInput.focus();
                            }
                        }"
                    x-ref="searchInput"
                    x-init="
                            $nextTick(() => focusInput());
                            document.addEventListener('visibilitychange', () => {
                                if (!document.hidden) {
                                    setTimeout(() => focusInput(), 100);
                                }
                            });
                        "
                    x-on:keydown.enter="$wire.addByBarcode($event.target.value); $event.target.value=''; $nextTick(() => focusInput())"
                    wire:model.live="search"
                    placeholder="Skanerlash yoki qo'lda kiriting..."
                    autofocus
                />
            </x-filament::input.wrapper>

            {{-- Qidiruv natijalari --}}
            @if($products->isNotEmpty())
                <table class="w-full mt-4 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-2 py-1 text-left">Barcode</th>
                        <th class="px-2 py-1 text-left">Nomi</th>
                        <th class="px-2 py-1 text-right">Narxi</th>
                        <th class="px-2 py-1"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $p)
                        <tr wire:key="item-{{ $p->id }}" class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-200">
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

        {{-- Right Column: Current Cart --}}
        <x-filament::card class="lg:sticky lg:top-6 h-fit" > {{-- Sticky for desktop --}}
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Savat #{{ $activeCartId }}</h2>
                @if(isset($totals['qty']) && $totals['qty'] > 0)
                    <span class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">{{ $totals['qty'] }} mahsulot</span>
                @endif
            </div>

            @if(empty($cart))
                <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-shopping-cart class="w-16 h-16 mx-auto mb-3 text-gray-400 dark:text-gray-500"/>
                    <p>Savatda hozircha mahsulot yo'q.</p>
                    <p class="text-xs mt-1">Qidiruv maydonidan mahsulot qo'shing.</p>
                </div>
            @else
                <div class="flow-root">
                    <table class="w-full text-sm divide-y divide-gray-200 dark:divide-gray-700"> {{-- Changed min-w-full to w-full --}}
                        <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="w-full px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomi</th> {{-- Added w-full --}}
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Miqdori</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Narx</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jami</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amal</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($cart as $index => $row)
                            <tr>
                            <td class="w-full px-3 py-3 font-medium text-gray-900 dark:text-gray-100 whitespace-normal break-words">{{ $row['name'] }}</td> {{-- Added w-full --}}
                                <td class="px-3 py-3 text-center">
                                    <input type="number" min="1"
                                           x-on:change="$wire.updateQty({{ $row['id'] }}, $event.target.value);"
                                           value="{{ $row['qty'] }}"
                                           class="w-20 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 rounded-md shadow-sm text-center py-1.5 px-2 text-sm">
                                </td>
                                <td class="px-3 py-3 text-right text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ number_format($row['price'], 0,'.',' ') }}</td>
                                <td class="px-3 py-3 text-right font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ number_format($row['qty'] * $row['price'], 0,'.',' ') }}</td>
                                <td class="px-3 py-3 text-center">
                                    <button wire:click="remove({{ $row['id'] }})" class="text-danger-600 hover:text-danger-800 dark:text-danger-500 dark:hover:text-danger-400 p-1.5 rounded-md hover:bg-danger-50 dark:hover:bg-danger-900/50" title="O'chirish">
                                        <x-heroicon-o-trash class="w-5 h-5"/>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- === Totallar va tugmalar === --}}
                <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4 space-y-2">

                    {{-- Totallar --}}
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Mahsulotlar soni:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $totals['qty'] }} dona</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold text-gray-900 dark:text-white">
                        <span>Jami summa:</span>
                        <span>{{ number_format($totals['amount'], 0, '.', ' ') }} so'm</span>
                    </div>

                    {{-- Tugmalar satri --}}
                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        {{-- Chapda — yopish (kichik, qizil) --}}
                        <x-filament::button
                            wire:click="closeCart({{ $activeCartId }})"
                            size="sm"
                            color="danger"
                            class="sm:w-auto w-full order-2 sm:order-1"
                            wire:confirm="Savat #{{ $activeCartId }} ni yopishni tasdiqlaysizmi?"
                        >
                            Yopish
                        </x-filament::button>

                        {{-- O‘ngda — to‘lovni yakunlash (katta, yashil) --}}
                        <x-filament::button
                            wire:click="checkout"
                            color="success"
                            size="lg"
                            icon="heroicon-o-check-circle"
                            class="sm:w-auto w-full order-1 sm:order-2"
                        >
                            Savat #{{ $activeCartId }} ni yakunlash
                        </x-filament::button>
                    </div>
                </div>
            @endif
        </x-filament::card>
    </div>
</x-filament::page>


