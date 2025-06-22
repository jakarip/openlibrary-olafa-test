<?php

namespace App\Http\Controllers\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;

class MailController extends Controller
{
    public function sendEmail()
    {
        $details = [
            'subject' => 'Test Email dari Laravel',
            'title' => 'Halo, ini Laravel!',
            'message' => 'Ini adalah email percobaan dalam format HTML dari Laravel Mailer.'
        ];

        Mail::to('dafiiutomo@gmail.com')->send(new SendEmail($details));

        return response()->json(['message' => 'Email berhasil dikirim dalam format HTML!']);
    }
}
