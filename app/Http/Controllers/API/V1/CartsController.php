<?php

namespace App\Http\Controllers\API\V1;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartsResource;
use App\Mail\OrderShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Order;
use App\OrderItem;

class CartsController extends Controller
{
    /**
     * carts property.
     */
    protected $carts;

    /**
     * Constructor.
     *
     * @param \App\Cart; $carts
     */
    public function __construct(Cart $carts)
    {
        $this->carts = $carts;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts = $this->carts->with('item')->get();

        return CartsResource::collection($carts);
    }

    /**
     * Delete all registers.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleleAll()
    {
        $this->carts->truncate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item = $this->carts->where('product_colors_id', $request->product_colors_id);
        if ($item->count()) {
            $item->increment('quantity');
            $item = $item->first();
        } else {
            $item = $this->carts->create([
                'product_colors_id' => $request->product_colors_id,
                'quantity'          => 1,
            ]);
        }

        return new CartsResource($item);
    }

    /**
     * Send mail of cart.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function checkout(Request $request)
    {
      $order =  Order::create([
            'user_id'=> $request->user()->id,
            'quantity' => $request->count,
            'amount_paid' => $request->total,
        ]);

        foreach ($request->cart as $key => $value) {
            OrderItem::create([
               'order_id' => $order->id,
               'product_id' => $value['product']['id'],
               'quantity' => $value['quantity']
            ]);
        }

       // Mail::to($request->user()->email)->send(new OrderShipped($request->all()));

        return  Order::find($order->id);
        
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Cart $cart
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, Request $request)
    {
        $item = $this->carts->where('product_colors_id', $productId)->first();
        $item->decrement('quantity');
        if ($item->quantity === 0) {
            $item->delete();
        }

        return response(null, 200);
    }

    public function updateOrder(Request $request)
    {
     if ($this->verifyPayment($request->payment_refernce)) {
        return Order::find($request->order_id)->update([
            'payment_status' => true,
            'reference' => $request->payment_refernce,
        ]);
     }
    
       // return $request->payment_refernce;
    }

    public function verifyPayment($reference)
    {

        if (!$reference) {
            return;
        }

        $url = 'https://api.paystack.co/transaction/verify/' . $reference;
       // $secretKey = env('PAYSTACK_SECRET', null);
        $secretKey = 'sk_test_482f65dca918205f2d449a83fdf33ae0a10420cf';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $secretKey"]
        );
        $request = curl_exec($ch);
        if (curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, true);
        }

        if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
            return true;
        }else {
           return false; # code...
        }
    
    }
}
