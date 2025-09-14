<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Registration;
use App\Services\EmailService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Correct import
use Illuminate\Support\Facades\Mail;



class TestController extends Controller
{
    public function index()
    {
        $message = "test";
        $subject = "Test";
        $html = "test";
        $registration = "test";
        
        
        Mail::send([], [], function ($message) use ($registration, $subject, $html) {
        $message->to("anjitsibakoti09@gmail.com")
            ->subject($subject)
            ->from('rpmun@rps.edu.np', 'RPMUN Registration')
            ->cc(['asibakoti@ren.org.np', 'dilip.nepal@rps.edu.np' , 'basanta.lamichhane@rps.edu.np'])
            ->html($html);
    });
    
    
    }
}
