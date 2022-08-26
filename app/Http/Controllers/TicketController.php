<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Reply;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmail;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function ticket()
    {
        $data['title'] = 'Tickets';
        $data['ticket'] = Ticket::whereUser_id(Auth::guard('user')->user()->id)->latest()->paginate(4);
        return view('user.support.index', $data);
    }
    public function openticket()
    {
        $data['title'] = 'New Ticket';
        return view('user.support.new', $data);
    }
    public function Replyticket($id)
    {
        $data['ticket'] = $ticket = Ticket::whereid($id)->first();
        $data['title'] = '#' . $ticket->ticket_id;
        $data['reply'] = Reply::whereTicket_id($ticket->ticket_id)->get();
        return view('user.support.reply', $data);
    }
    public function Destroyticket($id)
    {
        $data = Ticket::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Request was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Request');
        }
    }
    public function Resolveticket($id)
    {
        $ticket = Ticket::find($id);
        $ticket->status = 1;
        $ticket->save();
        return back()->with('success', 'Ticket has been closed.');
    }
    public function submitticket(Request $request)
    {
        $set = Settings::first();
        if ($request->has('ref_no')) {
            $check = Ticket::whereref_no($request->ref_no)->count();
            if ($check > 0) {
                return back()->with('alert', 'You have already raised a dispute on this.');
            } else {
                if ($request->hasfile('image')) {
                    $validator = Validator::make($request->all(), [
                        'image.*' => 'mimes:doc,pdf,docx,zip,png,jpeg'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->route('transfererror')->withErrors($validator)->withInput();
                    } else {
                        foreach ($request->file('image') as $file) {
                            $token = str_random(10);
                            $name = 'support_' . $token . '.' . $file->extension();
                            $file->move(public_path('asset/profile'), $name);
                            $data[] = $name;
                            $sav['files'] = json_encode($data);
                        }
                    }
                }
                $user = $data['user'] = User::find(Auth::guard('user')->user()->id);
                $sav['user_id'] = Auth::guard('user')->user()->id;
                $sav['business_id'] = Auth::guard('user')->user()->business_id;
                $sav['subject'] = $request->subject;
                $sav['priority'] = $request->priority;
                $sav['type'] = $request->type;
                $sav['message'] = $request->details;
                $sav['ref_no'] = $request->ref_no;
                $sav['ticket_id'] = $token = 'DIS-' . str_random(6);
                $sav['status'] = 0;
                Ticket::create($sav);
                if ($set['email_notify'] == 1) {
                    dispatch(new SendEmail($user->email, $user->username, 'New Ticket - ' . $request->subject, "Thank you for contacting us, we will get back to you shortly, your Ticket ID is " . $token));
                    dispatch(new SendEmail($set->support_email, $set->site_name, 'New Ticket:' . $token, "New ticket request"));
                }
                return redirect()->route('user.ticket')->with('success', 'Ticket Submitted Successfully.');
            }
        } else {
            if ($request->hasfile('image')) {
                $validator = Validator::make($request->all(), [
                    'image.*' => 'mimes:doc,pdf,docx,zip,png,jpeg'
                ]);
                if ($validator->fails()) {
                    return redirect()->route('transfererror')->withErrors($validator)->withInput();
                } else {
                    foreach ($request->file('image') as $file) {
                        $token = str_random(10);
                        $name = 'support_' . $token . '.' . $file->extension();
                        $file->move(public_path('asset/profile'), $name);
                        $data[] = $name;
                        $sav['files'] = json_encode($data);
                    }
                }
            }
            $user = $data['user'] = User::find(Auth::guard('user')->user()->id);
            $sav['user_id'] = Auth::guard('user')->user()->id;
            $sav['business_id'] = Auth::guard('user')->user()->business_id;
            $sav['subject'] = $request->subject;
            $sav['priority'] = $request->priority;
            $sav['type'] = $request->type;
            $sav['message'] = $request->details;
            $sav['ref_no'] = $request->ref_no;
            $sav['ticket_id'] = $token = 'DIS-' . str_random(6);
            $sav['status'] = 0;
            Ticket::create($sav);
            if ($set['email_notify'] == 1) {
                dispatch(new SendEmail($user->email, $user->username, 'New Ticket - ' . $request->subject, "Thank you for contacting us, we will get back to you shortly, your Ticket ID is " . $token));
                dispatch(new SendEmail($set->support_email, $set->site_name, 'New Ticket:' . $token, "New ticket request"));
            }
            return redirect()->route('user.ticket')->with('success', 'Ticket Submitted Successfully.');
        }
    }
    public function submitreply(Request $request)
    {
        $set = Settings::first();
        $sav['reply'] = $request->details;
        $sav['ticket_id'] = $request->id;
        $sav['business_id'] = Auth::guard('user')->user()->business_id;
        $sav['status'] = 1;
        Reply::create($sav);
        if ($set['email_notify'] == 1) {
            dispatch(new SendEmail($set->email, $set->site_name, 'Ticket Reply:' . $request->id, "New ticket reply request"));
        }
        $data = Ticket::whereTicket_id($request->id)->first();
        $data->status = 0;
        $data->save();
        return back()->with('success', 'Message sent!.');
    }
}
