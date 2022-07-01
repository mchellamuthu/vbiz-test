<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function index() {
        $products = Product::all();
        return view('welcome',compact('products'));
    }


    public function showProduct(Product $product) {

        $intent = auth()->user()->createSetupIntent();



        return view('product',compact('product','intent'));


        
    }
    public function purchase(Request $request, Product $product)
    {
        $user          = $request->user();
        $paymentMethod = $request->input('payment_method');
        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);
            $user->charge($product->price * 100, $paymentMethod);
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect('/')->with('message', 'Product purchased successfully!');
    }
}
