<x-app-layout>
    <div class="wishlist-container">
        <h1>Your Wishlist</h1>
    
        @if($wishlistItems->isEmpty())
            <p>Your wishlist is empty.</p>
        @else
            <div class="wishlist-items">
                @foreach($wishlistItems as $item)
                    <div class="wishlist-item">
                        {{-- <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"> --}}
                        <div class="product-details">
                            <h2>{{ $item->product->name }}</h2>
                            <p>{{ $item->product->description }}</p>
                        </div>
                        <div class="product-actions">
                            <button class="mt-4 add-to-cart" onclick="toggleCart(this); removeFromWishlist({{ $item->product_id }})"
                                data-id="{{ $item->product->id }}" 
                                data-name="{{ $item->product->name }}" 
                                data-description="{{ $item->product->description }}"
                                data-price="{{ $item->product->price }}"
                            >
                                {{-- <span class="button-text">Add</span> --}}
                                <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="currentColor" d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2S15.9 22 17 22s2-.9 2-2-.9-2-2-2zM7.01 9h9.87l-.94 4H8.05l-1.04-4zm2-5h4l1 4h-6z"></path>
                                </svg>
                                <svg class="check-icon hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="currentColor" d="M9 16.2l-3.5-3.5-1.4 1.4L9 19l10-10-1.4-1.4z"></path>
                                </svg>
                            </button>
                            <button class="remove-button" onclick="removeFromWishlist({{ $item->product_id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 6l3 18h12l3-18H3zm5 2h2v12H8V8zm6 0h2v12h-2V8zM4 4h16v2H4V4zM10 2h4v2h-4V2z"/>
                                </svg>
                            </button>
                            
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div id="notification-dialog" class="notification">
        <span id="notification-message"></span>
    </div>
</x-app-layout>

<script>
    function toggleCart(button) {
        const cartIcon = button.querySelector('.cart-icon');
        const checkIcon = button.querySelector('.check-icon');

        // Switch icons
        cartIcon.classList.toggle('hidden');
        checkIcon.classList.toggle('hidden');

        // Retrieve item data
        const itemId = button.getAttribute('data-id');
        const itemName = button.getAttribute('data-name');
        const itemDescription = button.getAttribute('data-description');
        const itemPrice = button.getAttribute('data-price');

        // Send POST request
        fetch('/add-to-cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ id: itemId, name: itemName, description: itemDescription, price: itemPrice })
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message);  // Show the notification with the message
        })

        .catch(error => console.error('Error:', error));
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
        }, 2000);
    }
    function removeFromWishlist(productId) {
        fetch('/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            // Optionally, reload the page or remove the item element from the DOM
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
</script>
<style>
    .add-to-cart {
        background-color: black ;
        color: white ;
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        width: 30;
    }

    .add-to-cart:hover {
        background-color: black ;
    }
    .product-actions{
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    .notification {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background-color: hsl(180, 100%, 50%) ;
        color: black;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0px 4px 8px black (218, 217, 217, 0.2);
        opacity: 0; /* Starts as invisible */
        visibility: hidden; /* Ensure it's not interactable when hidden */
        z-index: 1000;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .notification.show {
        opacity: 1; 
        visibility: visible;
        transform: translateX(-50%) translateY(10px); /* Small movement */
    }
    /* Container for the entire wishlist page */
    .wishlist-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
        font-family: 'Arial', sans-serif;
        background-color: #000000;
        margin-top: 20px;
    }

    .wishlist-container h1 {
        font-size: 1.2rem;
        font-weight: bold;
        color: rgb(255, 255, 255);
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .wishlist-container p {
        font-size: .9rem;
        color: rgb(255, 255, 255);
        text-align: center;
    }

    /* Wishlist items wrapper */
    .wishlist-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    /* Individual wishlist item */
    .wishlist-item {
        background-color:aqua;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .wishlist-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .wishlist-item img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .product-details {
        text-align: center;
    }

    .product-details h2 {
        font-size: 1rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .product-details p {
        font-size: 1rem;
        color: #666;
        margin-bottom: 1rem;
    }

    /* Button styling */
    .remove-button {
        background-color: #e74c3c;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 0.5rem 1rem;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .remove-button:hover {
        background-color: #c0392b;
        transform: scale(1.05);
    }
</style>