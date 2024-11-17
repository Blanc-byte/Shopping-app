    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .cart-item {
                border: 1px solid aqua; /* Tailwind's gray-300 */
                border-radius: 0.5rem; /* Tailwind's rounded-lg */
                padding: 1rem; /* Tailwind's p-4 */
                transition: box-shadow 0.3s;
            }

            .cart-item:hover {
                box-shadow: 0 4px 8px black (0, 0, 0, 0.1); /* Add shadow on hover */
            }

            .cart-header {
                font-weight: bold;
                font-size: 1.25rem; /* Tailwind's text-xl */
            }

            .cart-button {
                padding: 0.5rem 1rem; /* Tailwind's px-4 py-2 */
                border-radius: 0.375rem; /* Tailwind's rounded */
                color: black /* Black text */
            }

            .clear-cart {
                background-color: aqua; /* Tailwind's red-500 */
                margin-top: 0.5rem; /* Tailwind's mt-2 */
            }

            .place-order {
                background-color: aqua; /* Tailwind's green-500 */
                margin-left: 0.5rem; /* Tailwind's ml-2 */
            }

            /* Custom grid styles */
            .grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr); /* 4 equal columns */
                gap: 1rem; /* Space between grid items */
            }

            @media (max-width: 768px) {
                .grid {
                    grid-template-columns: repeat(2, 1fr); /* 2 columns on smaller screens */
                }
            }

            @media (max-width: 480px) {
                .grid {
                    grid-template-columns: 1fr; /* 1 column on extra small screens */
                }
            }
            #confirmation-modal {
                display: none; /* Hidden by default */
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
                z-index: 50; /* Ensure it's on top */
                align-items: center;
                justify-content: center;
            }

            /* Modal content */
            #confirmation-modal .modal-content {
                background-color:aqua;
                padding: 1.5rem;
                border-radius: 0.5rem;
                max-width: 400px;
                width: 90%;
                text-align: center;
            }
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
                opacity: 0; /* Starts as invisible */
                visibility: hidden; /* Ensure it's not interactable when hidden */
                z-index: 1000;
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            .notification.show {
                opacity: 1; /* Make it visible */
                visibility: visible;
                transform: translateX(-50%) translateY(10px); /* Small movement */
            }
        
        </style>
    </head>

    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Welcome ') }} {{ Auth::user()->name }}
            </h2>
        </x-slot>

        <div class="py-12 ">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if(count($cart) > 0)
                            <div class="grid"> <!-- Using the custom grid class -->
                                @php
                                    $totalPrice = 0; // Initialize total price variable
                                @endphp
                                @foreach($cart as $id => $item)
                                    @php
                                        $totalPrice += $item->price * $item->quantity; // Calculate total price
                                    @endphp
                                    <div class="cart-item">
                                        <h3 class="cart-header">{{ $item->name }}</h3>
                                        <p>{{ $item->description }}</p>
                                        <p>Price: ${{ number_format($item->price, 2) }}</p>
                                        <p>Quantity: {{ $item->quantity }}</p>
                                        <div class="flex items-center mt-2 gap-4 mt-8">
                                            @if ($item->quantity > 1)
                                                <button class="decrease-quantity text-red-500 border border-red-500 rounded px-2" data-id="{{ $item->productID }}">
                                                    -
                                                </button>
                                            @endif
                                            <span class="mx-2 text-lg font-semibold">{{ $item->quantity }}</span>
                                            <button class="increase-quantity text-green-500 border border-green-500 rounded px-2" data-id="{{ $item->productID }}">
                                                +
                                            </button>
                                            <button class="remove-from-cart text-red-500 ml-4 border border-red-500 rounded px-2" data-id="{{ $item->productID }}">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <h3 class="font-bold">Total Price: ${{ number_format($totalPrice, 2) }}</h3> <!-- Display total price -->
                                <button id="clear-cart" class="cart-button clear-cart">
                                    Clear Cart
                                </button>
                                <button id="place-order" class="cart-button place-order" onclick="placeOrder()">
                                    Place Order
                                </button>
                            </div>
                        @else
                            <p>Your cart is empty.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Confirmation Modal -->
        <div id="confirmation-modal" class="hidden flex items-center justify-center"> <!-- Added 'hidden' class here -->
            <div class="modal-content">
                <h3 class="text-lg font-semibold mb-4">Are you sure?</h3>
                <p class="mb-6">Do you really want to clear your cart?</p>
                <div class="flex justify-center gap-4">
                    <button id="confirm-clear-cart" class="px-4 py-2 bg-red-500 text-gray-700 rounded">Yes</button>
                    <button id="cancel-clear-cart" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">No</button>
                </div>
            </div>
        </div>
        <!-- Notification Dialog -->
        <div id="notification-dialog" class="notification">
            <span id="notification-message"></span>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function placeOrder(){
            fetch("{{ route('order.placeOrder') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ /* include any necessary data, e.g., cart items */ })
            })
            .then(response => response.json())
            .then(data => {

                // Show notification
                showNotification(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        function showNotification(message) {
            const notificationDialog = document.getElementById('notification-dialog');
            const notificationMessage = document.getElementById('notification-message');

            // Set the message text
            notificationMessage.textContent = message;

            // Show the notification
            notificationDialog.classList.add('show');

            // Hide the notification after 2 seconds
            setTimeout(() => {
                notificationDialog.classList.remove('show');
            }, 900);
        }
        $(document).ready(function() {
            // Increase quantity
            $(document).on('click', '.increase-quantity', function() {
                let productId = $(this).data('id');

                $.ajax({
                    url: `/cart/increase/${productId}`,
                    method: 'PATCH',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            // Decrease quantity
            $(document).on('click', '.decrease-quantity', function() {
                let productId = $(this).data('id');

                $.ajax({
                    url: `/cart/decrease/${productId}`,
                    method: 'PATCH',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            // Remove item from cart
            $(document).on('click', '.remove-from-cart', function() {
                let productId = $(this).data('id');

                $.ajax({
                    url: `/cart/remove/${productId}`,
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            $('#clear-cart').on('click', function() {
                $('#confirmation-modal').css('display', 'flex');
            });

            // Confirm clear cart
            $('#confirm-clear-cart').on('click', function() {
                $.ajax({
                    url: '/cart/clear',
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        $('#confirmation-modal').css('display', 'none');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error clearing cart: ' + xhr.responseText);
                        $('#confirmation-modal').css('display', 'none');
                    }
                });
            });

            // Cancel clear cart
            $('#cancel-clear-cart').on('click', function() {
                $('#confirmation-modal').css('display', 'none');
            });
    });

    </script>

    </x-app-layout>
