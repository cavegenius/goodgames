<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function submit(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $rules = [
            'name' => 'required',
            'comments' => 'required',
        ];
        $this->validate($request, $rules);

        $name = $request->get('name');
        $email = $request->get('email');
        $comments = $request->get('comments');

        $data = array('name' => $name,'email' => $email,'comments' => $comments);

        Mail::send('emails.feedback', $data, function($message) {
            $message->to('wyatt@cavegenius.com')->subject('Feedback from Your Game Lists');
            //$message->from('feedback@yourgamelists.com','Your Game Lists');
        });
        
        $response = json_encode(['Status' => 'Success', 'Message' => 'Your feedback has been submitted successfully']);
        return $response;
    }
}
