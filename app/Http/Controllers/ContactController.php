<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|phone:NG',
            'subject' => 'required|string',
            'message' => 'required|string',
        ],
        [
            'phone.phone' => 'The phone number must be a valid Nigerian phone number.',
        ]
    );

        Mail::to("ochigbogodswill868@gmail.com")->send(new ContactFormMail($data));

        return response()->json(['success' => 'Message sent successfully!']);
    }
}
