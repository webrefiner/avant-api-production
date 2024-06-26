<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Receipt') }}
        </h2>
    </x-slot>

    <div class="flex justify-end gap-6">
        {{-- Print button --}}
        <div class="flex justify-end">
            <button type="button"
                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-md hover:bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500"
                onclick="printDiv('printarea')">
                Print Full Size
            </button>
        </div>

        {{-- Print Button 2 --}}
        <div class="flex justify-end">
            <button type="button"
                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-md hover:bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500"
                onclick="printDiv('printareasmall')">
                Print Receipt 
            </button>
        </div>
    </div>

    <div class="my-12 p-4 bg-white rounded-md shadow-lg" id="printarea">

        <div class="grid grid-cols-1 sm:grid-cols-12">
            <div class="sm:hidden">
                <img src="{{ url('/img/school_logo.png') }}" alt="school_logo" class="h-20 mx-auto">
            </div>
            <div class="sm:col-start-3 sm:col-span-8 text-center">
                <span class="font-bold">{{ $variables['ADDRESS_LINE_1'] }}</span> <br>
                {{ $variables['ADDRESS_LINE_2'] }}
                {{ $variables['ADDRESS_LINE_3'] }}<br>
                School Reg. ID: {{ $variables['SCHOOL_REG_ID'] }}
            </div>
            <div class="hidden sm:block">
                <img src="{{ url('/img/school_logo.png') }}" alt="school_logo" class="">
            </div>
        </div>

        <div class="flex justify-between items-baseline">
            <h2 class="text-sm font-semibold pt-1 pb-3">Receipt No: {{ $counterReceipt->id }}</h2>
            <h3 class="text-sm font-semibold pt-1 pb-3">Receipt Date: {{ $counterReceipt->created_at->format('d-m-Y') }}
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="standard_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"> Standard
                </label>
                <input type="text" id="standard_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2.5 py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $counterReceipt->standard->name }}" readonly>
            </div>

            <div>
                <label for="student_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Student
                </label>
                <input type="text" id="student_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2.5 py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $counterReceipt->student->user->userDetail->name }} (Roll: {{ $counterReceipt->student->roll_no }})"
                    readonly>
            </div>

            <div class="sm:col-span-3">
                <label for="remarks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"> Remarks
                </label>
                <p class="block px-2.5 py-1.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    readonly>{{ $counterReceipt->remarks }}</p>
            </div>
        </div>

        <div class="mt-4">
            <h2 class="text-lg font-bold pt-1.5">Items</h2>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                        Sl
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                        Item
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                        Amount (in Rs.)
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                                @foreach ($counterReceipt->counterReceiptItems as $item)
                                    <tr>
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $item->counterReceiptItemType->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $item->amount_in_cents / 100 }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- Total  --}}
                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                        Total:
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                        ₹ {{ $counterReceipt->totalAmount() }}/- <br>
                                        <span class="font-bold">
                                            {{ Helper::convertToInrInWords($counterReceipt->totalAmount()) }}
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-sm pt-3">
            Print date: {{ now()->format('d-m-Y') }}
        </p>
    </div>

    {{-- Preint area printareasmall for 58mm thermal paper --}}
    <div id="printareasmall" class="hidden">
        {{-- School logo & name --}}
        <div class="p-0.5">
            <div class="">
                <img src="{{ url('/img/school_logo.png') }}" alt="school_logo" class="mx-auto h-24">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-12">
                <div class="sm:col-start-3 sm:col-span-8 text-center text-xs font-semibold leading-none">
                    <div>{{ $variables['ADDRESS_LINE_1'] }}</div>
                    <div class="pt-2">
                        Reg. ID: {{ $variables['SCHOOL_REG_ID'] }}
                    </div>
                </div>
            </div>

            <div class="">
                <div class="flex justify-between mt-4">
                    <div class="flex flex-col">
                        <p class="text-xs">Receipt No: {{ $counterReceipt->id }}</p>
                        <p class="text-xs">Rcpt Date: {{ $counterReceipt->created_at->format('d-m-Y') }}</p>
                        <p class="mt-2 text-xs">Name:
                            {{ $counterReceipt->student->user->userDetail->name }}
                        </p>
                        <div class="flex justify-between">
                            <p class="text-xs">Class: {{ $counterReceipt->standard->name }}</p>
                            <p class="text-xs">Roll: {{ $counterReceipt->student->roll_no }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mt-2">
                        <p class="text-xs font-bold">Items</p>
                    </div>
                    <hr class="border-black">
                    <div>
                        @foreach ($counterReceipt->counterReceiptItems as $item)
                            <div class="flex justify-between">
                                <p class="text-xs">{{ $item->counterReceiptItemType->name }}</p>
                                <p class="text-xs">₹ {{ $item->amount_in_cents / 100 }}/-</p>
                            </div>
                            <hr class="border-black">
                        @endforeach
                    </div>
                    <div class="flex justify-between">
                        <p class="text-xs font-semibold">Total</p>
                        <p class="text-xs font-semibold">₹ {{ $counterReceipt->totalAmount() }}/-</p>
                    </div>
                    <div class="mt-3">
                        <p class="text-xs font-semibold uppercase">
                            {{ Helper::convertToInrInWords($counterReceipt->totalAmount()) }} Only
                        </p>
                    </div>
                    @if ($counterReceipt->remarks != null)
                        <div class="mt-3">
                            <p class="text-xs">
                                Remarks: {{ $counterReceipt->remarks }}
                            </p>
                        </div>
                    @endif
                </div>
                <p class="pt-3" style="font-size: 11px;">
                    Print date: {{ now()->format('d-m-Y') }}
                </p>
            </div>
        </div>
    </div>




    <script>
        function printDiv(div_id) {
            var printContents = document.getElementById(div_id).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</x-app-layout>
