<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome ') }} {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($orders && count($orders) > 0)
                        <div class="overflow-x-auto flex justify-center">
                            <table class="min-w-full divide-y divide-gray-200 text-sm text-center">
                                <thead class="bg-gray-100 text-gray-600">
                                    <tr>
                                        <th class="px-4 py-2 text-xs font-medium uppercase tracking-wider">Product</th>
                                        <th class="px-4 py-2 text-xs font-medium uppercase tracking-wider">Description</th>
                                        <th class="px-4 py-2 text-xs font-medium uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-2 text-xs font-medium uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-2 text-xs font-medium uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-2 text-xs font-medium uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $order->name }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $order->description }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $order->quantity }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">${{ number_format($order->total, 2) }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($order->Date)->format('Y-m-d') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $order->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            {{ __("No orders made yet!") }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
