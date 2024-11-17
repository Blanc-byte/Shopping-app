<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function viewOrderDetails()
    {
        $orders = DB::select('SELECT name, description,quantity, total, p.created_at as Date, status
                    FROM orders o
                    JOIN products p ON o.product_id = p.id');

        return view('user.orderDetails',['orders' => $orders]);
    }
    public function filterByCategory(Request $request)
    {
        $category = $request->query('category');
        $sort = $request->query('sort', 'name'); // Default sort by name if not provided
        $order = $request->query('order', 'asc'); // Default order is ascending

        $productsQuery = Product::query();

        if ($category) {
            $productsQuery->where('category', $category);
        }

        // Apply sorting
        $products = $productsQuery->orderBy($sort, $order)->get();

        return response()->json(['products' => $products]);
    }

    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if the product already exists in the wishlist for the authenticated customer
        $wishlistItem = Wishlist::where('customer_id', Auth::id())
                                ->where('product_id', $request->product_id)
                                ->first();

        if ($wishlistItem) {
            // If the item exists and its status is 0, update the status to 1
            if ($wishlistItem->status == 0) {
                $wishlistItem->status = 1;
                $wishlistItem->save();
                return response()->json(['message' => 'Product status updated to active in wishlist']);
            }

            // If the item already exists and the status is 1, do nothing
            return response()->json(['message' => 'Product already in wishlist']);
        }

        // If the item doesn't exist in the wishlist, create a new entry
        $wishlist = Wishlist::create([
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Product added to wishlist successfully']);
    }

    public function removeFromWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        // Find the wishlist item for the authenticated user and product
        $wishlistItem = Wishlist::where('customer_id', Auth::id())
        ->where('product_id', $request->product_id)
        ->first();

        // If the item is found, update its status to 0 (removed)
        if ($wishlistItem) {
            $wishlistItem->status = 1;
            $wishlistItem->save();
        }
        
        return response()->json(['message' => 'Product removed from wishlist']);
    }
    public function viewWishlist()
    {
        // Fetch wishlist items for the authenticated user
        $wishlistItems = Wishlist::with('product')
        ->where('customer_id', Auth::id())
        ->where('status', 1)  // Only show active items
        ->get();

        // Pass the wishlist items to the view
        return view('user.wishlist', compact('wishlistItems'));
    }

    public function index()
    {
        $categories = DB::select('SELECT category FROM products GROUP BY category');

        $products = product::all();

        return view('user.dashboard',[
            'products' => $products,
            'categories' => $categories]);
    }
    public function addToCart(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'id' => 'required|integer',
        ]);


        $userId = Auth::id();
        $productId = $request->input('id');

        // Check if the item is already in the user's cart
        $existingItem = DB::table('carts')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (!$existingItem) {
            // Add item to adtocart table
            DB::table('carts')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            return response()->json(['message' => 'Item added to cart successfully!']);
        } else {
            return response()->json(['message' => 'Item already in cart!']);
        }
    }
    public function placeOrder(Request $request)
    {
        session()->forget('cart');

        return response()->json(['message' => 'Order placed successfully.']);
    }
    public function increaseQuantity($productId)
    {
        $userId = Auth::id();
        DB::table('carts')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->increment('quantity');

        return response()->json(['message' => 'Quantity increased']);
    }

    public function decreaseQuantity($productId)
    {
        $userId = Auth::id();
        $cartItem = DB::table('carts')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem->quantity > 1) {
            DB::table('carts')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->decrement('quantity');
            return response()->json(['message' => 'Quantity decreased']);
        }

        return response()->json(['message' => 'Minimum quantity reached'], 400);
    }

    public function removeItem($productId)
    {
        $userId = Auth::id();
        DB::table('carts')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Item removed']);
    }

    public function clearCart()
    {
        $userId = Auth::id();
        DB::table('carts')
            ->where('user_id', $userId)
            ->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function showCart()
    {
        $userId = Auth::id();
        
        // Retrieve cart items for the authenticated user
        $cartItems = DB::select(
            'SELECT p.name AS name, p.description AS description, p.price AS price, c.quantity as quantity, p.id as productID
            FROM carts c
            JOIN products p ON p.id = c.product_id
            WHERE c.user_id = ?', [$userId] );

        // Pass the cart items to the view
        return view('user.cart', ['cart' => $cartItems]);
    }
    public function insertOrder(Request $request)
    {
        
        $customer_id = auth()->id();
    
        
        $cartItems = DB::select(
                'SELECT product_id, user_id, quantity, price
                FROM carts c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?', [$customer_id] );

        
        foreach ($cartItems as $item){
            $total = $item->quantity * $item->price;

            DB::table('orders')
            ->insert(['customer_id' => $item->user_id,
            'product_id'  => $item->product_id,
            'quantity'    => $item->quantity,
            'price'       => $item->price,
            'total'       => $total,
        ]);
        
        }
        DB::table('carts')->where('user_id', $customer_id)->delete();
        return response()->json(['message' => 'Order placed successfully!'], 200);
    }


}   
