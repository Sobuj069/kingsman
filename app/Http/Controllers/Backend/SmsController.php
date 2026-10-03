<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apiKey = env('SMS_API_KEY');
        $url = env('SMS_BALANCE_URL') . '/' . $apiKey . '/getBalance';

        // Fetch the balance using an HTTP GET request
        $response = Http::get($url);

        if ($response->successful()) {
            $balance = $response->body(); // Adjust based on API response format
            // dd($balance);
        } else {
            $balance = 'Error fetching balance';
        }
        $customer = Customer::orderBy('id', 'asc')->get();
        return view('backend.pages.sms.index', compact('customer', 'balance'));
    }
    public function create() {}
    public function store(Request $request)
    {
        // dd($request->customer_name);
        if (env('APP_MODE') == 'demo') {
            session()->flash('error', __('This Feature is not available in Demo'));
            return back();
        } else {
            foreach ($request->customer_name as $customer) {
                $phone = Customer::find($customer);
                $contacts = $phone->phone;
                // $message = 'আসসালামু আলাইকুম, প্রিয় গ্রাহক, আপনার নিকট মেসার্স মেধা এন্টার প্রাইজ এর ০০০০ টাকা বাকি আছে  অনুগ্রহ করে পরিশোধ করুন, ধন্যবাদ';

                $response = sendPromotionalSMS($contacts, $request->message);

                if ($response == null) {
                    session()->flash('success', __('Sms Send successfully'));
                    return back();
                } else if ($response == 1002) {
                    session()->flash('error', __('Sender Id/Masking Not Found'));
                    return back();
                } else if ($response == 1003) {
                    session()->flash('error', __('API Not Found'));
                    return back();
                } else if ($response == 1004) {
                    session()->flash('error', __('SPAM Detected'));
                    return back();
                } else if ($response == 1005) {
                    session()->flash('error', __('Internal Error'));
                    return back();
                } else if ($response == 1006) {
                    session()->flash('error', 'Internal Error');
                    return back();
                } else if ($response == 1007) {
                    session()->flash('error', __('Balance Insufficient'));
                    return back();
                } else if ($response == 1008) {
                    session()->flash('error', __('Message is empty'));
                    return back();
                } else if ($response == 1009) {
                    session()->flash('error', __('Message Type Not Set (text/unicode)'));
                    return back();
                } else if ($response == 1010) {
                    session()->flash('error', __('Invalid User & Password'));
                    return back();
                } else if ($response == 1011) {
                    session()->flash('error', __('Invalid User Id'));
                    return back();
                } else if ($response == 1012) {
                    session()->flash('error', __('Invalid Number'));
                    return back();
                } else if ($response == 1013) {
                    session()->flash('error', __('API limit error'));
                    return back();
                } else if ($response == 1014) {
                    session()->flash('error', __('No matching template'));
                    return back();
                } else if ($response == 1015) {
                    session()->flash('error', __('SMS Content Validation Fails'));
                    return back();
                } else if ($response == 1016) {
                    session()->flash('error', __('IP address not allowed!!'));
                    return back();
                } else if ($response == 1019) {
                    session()->flash('error', __('Sms Purpose Missing'));
                    return back();
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
