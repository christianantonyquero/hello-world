<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendFeedback;

// A Controller for random pages
class StaticPagesController extends Controller
{
    public function terms_service(Request $request) {
        return view('static-pages.terms-service');
    }
    public function privacy_policy(Request $request) {
        return view('static-pages.privacy-policy');
    }
    public function shipping_returns(Request $request) {
        return view('static-pages.shipping-returns');
    }
    public function email_us(Request $request) {
        return view('static-pages.email-us');
    }
    public function send_email(Request $request) {
        Mail::to('support@ldrvillage.com', 'LDR Village')->send(new SendFeedback($request->email, $request->message));
        flash('Feedback received. Thank you for being with us.');
        return view('static-pages.thank-you');
    }
}
