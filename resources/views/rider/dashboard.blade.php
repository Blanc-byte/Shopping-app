<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="font-semibold text-xl leading-tight mb-4">{{ __("Pending Orders") }}</h2>

                    @if($pendingOrders->isEmpty())
                        <p>{{ __('No pending orders found.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingOrders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->customerName }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->total }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->Date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->status }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" 
                                                        style="background-color: white; 
                                                                color: black; 
                                                                font-weight: bold; 
                                                                padding: 0.25rem 0.5rem; 
                                                                border-radius: 0.375rem; 
                                                                transition: background-color 0.5s;
                                                                width:5rem;" 
                                                        onmouseover="this.style.backgroundColor='#1d4ed8'" 
                                                        onmouseout="this.style.backgroundColor='white'">
                                                    Get
                                                </button>

                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>
