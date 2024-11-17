<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="font-semibold text-xl leading-tight mb-4">{{ __("Orders to be Delivered") }}</h2>

                    @if($toBeDeliveredOrders->isEmpty())
                        <p>{{ __('No orders to be delivered found.') }}</p>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($toBeDeliveredOrders as $order)
                                    <tr id="order-row-{{ $order->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->CusName }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->total }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->Date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button 
                                                style="color: black; transition: color 0.5s; display:flex; justify-content: center; align-items: center" 
                                                onmouseover="this.style.color='#1e40af'" 
                                                onmouseout="this.style.color='black'"
                                                onclick="markAsDelivered({{ $order->id }}, {{ $order->origOrdersId }})"
                                                title="Mark as Delivered"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 2rem; width: 2rem;" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414 0L9 12.586 7.707 11.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 000-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

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
    
    <!-- Notification Dialog -->
    <div id="notification-dialog" class="notification">
        <span id="notification-message"></span>
    </div>
</x-app-layout>

<style>
    .notification {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background-color: #3b82f6;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        z-index: 1000;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .notification.show {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(10px);
    }
</style>

<script>
    function markAsDelivered(orderId, origOrdersId) {
        fetch(`/orders/${orderId}/${origOrdersId}/deliver`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message);
                
                // Update the row or remove it from the table
                const row = document.getElementById(`order-row-${orderId}`);
                if (row) row.remove();
            } else {
                alert('Failed to mark as delivered');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function showNotification(message) {
        const notificationDialog = document.getElementById('notification-dialog');
        const notificationMessage = document.getElementById('notification-message');

        notificationMessage.textContent = message;
        notificationDialog.classList.add('show');

        setTimeout(() => {
            notificationDialog.classList.remove('show');
        }, 2000);
    }
</script>
