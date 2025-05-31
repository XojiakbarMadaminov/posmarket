<x-filament::page>
    {{-- Qidiruv --}}
    <div class="space-y-6 lg:grid lg:grid-cols-2 lg:gap-8 lg:space-y-0">
        <div>
            <x-filament::input
                x-data
                x-on:keydown.enter="$wire.addByBarcode($event.target.value); $event.target.value='';"
                placeholder="Skanerlash yoki qo‘lda kiriting..."
            />

            {{-- Natijalar --}}
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
                                    Qo‘shish
                                </x-filament::button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Savat --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <h2 class="font-semibold mb-3">Savat</h2>

            @if(empty($cart))
                <div class="text-gray-500">Mahsulot yo‘q</div>
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
                                       wire:change="updateQty({{ $row['id'] }}, $event.target.value)"
                                       value="{{ $row['qty'] }}"
                                       class="w-16 border rounded px-1 text-center">
                            </td>
                            <td class="text-right">{{ number_format($row['price'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['qty'] * $row['price'], 2) }}</td>
                            <td class="text-right">
                                <button wire:click="remove({{ $row['id'] }})" class="text-red-600">×</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="border-t mt-3 pt-3 text-right space-x-6">
                    <span class="font-medium">Dona: {{ $totals['qty'] }}</span>
                    <span class="font-semibold">Summa: {{ number_format($totals['amount'], 2, '.', ' ') }} so‘m</span>
                </div>

                <x-filament::button wire:click="checkout" color="success" class="mt-4 w-full">
                    To‘lovni yakunlash
                </x-filament::button>
            @endif
        </div>
    </div>
</x-filament::page>
