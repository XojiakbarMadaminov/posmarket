<x-filament::page>
    <form wire:submit.prevent="updateStats" class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-full md:w-1/2 flex flex-col md:flex-row items-center gap-6">
                <div class="w-full">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2 block">{{__('Boshlanish sanasi')}}</label>
                    <div class="relative">
                        <x-filament::input
                            type="date"
                            wire:model.defer="start_date"
                            class="pl-10 w-full p-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        />
                    </div>
                </div>

                <div class="w-full">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2 block">{{__('Tugash sanasi')}}</label>
                    <div class="relative">
                        <x-filament::input
                            type="date"
                            wire:model.defer="end_date"
                            class="pl-10 w-full p-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        />
                    </div>
                </div>

                <div class="mt-6 md:mt-0">
                    <x-filament::button
                        type="submit"
                        class="w-full md:w-auto px-6 py-2.5 text-sm font-medium bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 text-white rounded-lg transition-all duration-200 flex items-center justify-center"
                    >
                        {{__('Filter')}}
                    </x-filament::button>
                </div>
            </div>
            <div class="w-full md:w-1/2"></div>
        </div>
    </form>
</x-filament::page>
