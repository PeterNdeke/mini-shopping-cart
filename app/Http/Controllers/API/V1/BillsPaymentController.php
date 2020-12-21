<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillsPaymentController extends Controller
{
    //

    public function billsPayment(Request $request)
    {
     $transactions = $this->paymentVerify($request->transaction_id);
    $result = $this->payBills($transactions);
     return $result;
    }

    private function paymentVerify($transactionID)
    {
        $curl = curl_init();

  curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionID/verify",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer FLWSECK_TEST-c4bce0a04e343bb168c82c06568a3fab-X"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;

    }

    private function payBills($transactions)
    {
  $url = " https://api.flutterwave.com/v3/bills";
  $fields = [
    'country' => "NG",
    'customer' => '+2347032796924',
    'amount' => '200',
    'recurrence' => "ONCE",
    'currency' => "NGN",
    "type"=> "AIRTIME",
    "reference"=> '12345'
  ];
  $fields_string = http_build_query($fields);
  //open connection
  $ch = curl_init();
  
  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer FLWSECK_TEST-c4bce0a04e343bb168c82c06568a3fab-X",
    "Cache-Control: no-cache",
  ));
  
  //So that curl_exec returns the contents of the cURL; rather than echoing it
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
  
  //execute post
  $result = curl_exec($ch);
  return $result;

    }
}
