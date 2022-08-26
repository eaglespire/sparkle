<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Admin;
use App\Models\Settings;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\Review;
use App\Models\Reply;
use App\Models\Business;
use App\Models\Exttransfer;
use App\Models\Transactions;
use App\Models\Paymentlink;
use App\Models\Audit;
use App\Models\Virtual;
use App\Models\Virtualtransactions;
use App\Jobs\SendPromoJob;
use App\Jobs\SendEmail;
use App\Jobs\approvedCompliance;
use App\Jobs\declinedCompliance;
use App\Jobs\rejectCompliance;
use App\Datatables\UserDataTable;

class CheckController extends Controller
{

        
    public function __construct()
    {		
        $this->middleware('auth');
    }

    public function Destroyuser($id)
    {
        Ticket::whereUser_id($id)->delete();
        Exttransfer::whereUser_id($id)->delete();
        Paymentlink::whereUser_id($id)->delete();
        Transactions::whereReceiver_id($id)->delete();
        User::whereId($id)->delete();
        Virtual::whereUser_id($id)->delete();
        Virtualtransactions::whereUser_id($id)->delete();
        Business::whereUser_id($id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'User was successfully deleted');
    }     
    
    public function Destroystaff($id)
    {
        $staff = Admin::whereId($id)->delete();
        return back()->with('success', 'Staff was successfully deleted');
    }  
        
    public function dashboard()
    {
        $data['title']='Dashboard';
        $data['totalusers']=User::count();
        $data['blockedusers']=User::whereStatus(1)->count();
        $data['activeusers']=User::whereStatus(0)->count();
        $data['totalticket']=Ticket::count();
        $data['openticket']=Ticket::whereStatus(0)->count();
        $data['closedticket']=Ticket::whereStatus(1)->count();        
        $data['totalreview']=Review::count();
        $data['pubreview']=Review::whereStatus(1)->count();
        $data['unpubreview']=Review::whereStatus(0)->count();         
        $data['pubmessage']=Contact::whereseen(1)->count();
        $data['unpubmessage']=Contact::whereseen(0)->count();        
        $data['messages']=Contact::count();
        return view('admin.dashboard.index', $data);
    } 

    public function searchUser(Request $request)
    {
        $data['title'] = "Clients";
        $data['users'] = User::where('first_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('last_name', 'LIKE', '%' . $request->search . '%')
        ->orwhere('email', 'LIKE', '%' . $request->search . '%')
        ->orderby('created_at', 'desc')->paginate(10);
        return view('admin.user.index', $data);
    }
    public function Paymentuser($id)
    {
        $data['title'] = "Payment";
        $data['links'] = Paymentlink::sortable()
        ->orderby('id', 'desc')
        ->wheremode(1)
        ->whereuser_id($id)
        ->paginate(10);
        return view('admin.payment.index', $data);
    }
    public function Transactionuser($id)
    {
        $data['title'] = "Transactions";
        $data['links'] = Transactions::sortable()
        ->orderby('id', 'desc')
        ->wheremode(1)
        ->wherereceiver_id($id)
        ->where('type', '!=', 3)
        ->paginate(10);
        return view('admin.payment.transactions', $data);
    }    
    public function Payoutuser($id)
    {
        $data['title'] = "Transactions";
        $data['links'] = Transactions::sortable()
        ->orderby('id', 'desc')
        ->wheremode(1)
        ->wherereceiver_id($id)
        ->wheretype(3)
        ->paginate(10);
        return view('admin.payment.transactions', $data);
    }
    
    public function Users()
    {
		$data['title']='Clients';
		$data['users']=User::latest()->paginate(10);
        return view('admin.user.index', $data);
    }    
    
    public function Staffs()
    {
		$data['title']='Staffs';
		$data['users']=Admin::where('id', '!=', 1)->latest()->get();
        return view('admin.user.staff', $data);
    }       

    public function Messages()
    {
		$data['title']='Messages';
		$data['message']=Contact::latest()->get();
        return view('admin.user.message', $data);
    }     

    public function Newstaff()
    {
		$data['title']='New Staff';
        return view('admin.user.new-staff', $data);
    }    
    
    public function Ticket()
    {
		$data['title']='Ticket system';
		$data['ticket']=Ticket::latest()->get();
        return view('admin.user.ticket', $data);
    }   
    
    public function Email($id,$name)
    {
		$data['title']='Send email';
		$data['email']=$id;
		$data['name']=$name;
        return view('admin.user.email', $data);
    }    
    
    public function Promo()
    {
		$data['title']='Send email';
        $data['client']=User::all();
        return view('admin.user.promo', $data);
    } 
    
    public function Sendemail(Request $request)
    {      
        dispatch(new SendEmail($request->to, $request->name, $request->subject, $request->message));   	
        return back()->with('success', 'Email will be sent');
    }
    
    public function Sendpromo(Request $request)
    {        	
        dispatch(new SendPromoJob($request->subject, $request->message)); 
        return back()->with('success', 'Email will be sent');
    }     
    
    public function Replyticket(Request $request)
    {      
        $ticket=Ticket::whereticket_id($request->ticket_id)->first();  
        $user=User::find($ticket->user_id);
        $data['ticket_id'] = $request->ticket_id;
        $data['reply'] = $request->reply;
        $data['status'] = 0;
        $data['staff_id'] = $request->staff_id;
        $data['business_id'] = $user->business_id;
        $res = Reply::create($data);  
        $set=Settings::first();   
        if($set['email_notify']==1){
            dispatch(new SendEmail($user->email, $user->username, 'New Reply - '.$request->ticket_id, $request->reply));
        } 
        if ($res) {
            return back();
        } else {
            return back()->with('alert', 'An error occured');
        }
    }    
    
    public function Createstaff(Request $request)
    {        
        $check=Admin::whereusername($request->username)->get();
        if(count($check)<1){
            $data['username'] = $request->username;
            $data['last_name'] = $request->last_name;
            $data['first_name'] = $request->first_name;
            $data['password'] = Hash::make($request->password);
            $data['profile'] = $request->profile;
            $data['support'] = $request->support;
            $data['promo'] = $request->promo;
            $data['message'] = $request->message;
            $data['settlement'] = $request->settlement;
            $data['country_supported'] = $request->country_supported;
            $data['knowledge_base'] = $request->knowledge_base;
            $data['language'] = $request->language;
            $data['email_configuration'] = $request->email_configuration;
            $data['general_settings'] = $request->general_settings;
            $data['news'] = $request->news;
            $data['payment'] = $request->payment;
            $data['transactions'] = $request->transactions;
            $data['vcard'] = $request->vcard;
            $res = Admin::create($data);  
            return redirect()->route('admin.staffs')->with('success', 'Staff was successfully created');
        }else{
            return back()->with('alert', 'username already taken');
        }
    }       
     
    public function Destroymessage($id)
    {
        $data = Contact::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Request was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Request');
        }
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

    public function Manageuser($id)
    {
        $data['client']=$user=User::find($id);
        $data['title']=$user->first_name.' '.$user->last_name;
        $data['audit']=Audit::whereUser_id($user->id)->orderBy('created_at', 'DESC')->get();
        return view('admin.user.edit', $data);
    }    
    
    public function Managestaff($id)
    {
        $data['staff']=$user=Admin::find($id);
        $data['title']=$user->username;
        return view('admin.user.edit-staff', $data);
    }    

    public function staffPassword(Request $request)
    {
        $user = Admin::whereid($request->id)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('success', 'Password Changed Successfully.');

    }
    
    public function Manageticket($id)
    {
        $data['ticket']=$ticket=Ticket::find($id);
        $data['title']='#'.$ticket->ticket_id;
        $data['client']=User::whereId($ticket->user_id)->first();
        $data['reply']=Reply::whereTicket_id($ticket->ticket_id)->get();
        return view('admin.user.edit-ticket', $data);
    }    
    
    public function Closeticket($id)
    {
        $ticket=Ticket::find($id);
        $ticket->status=1;
        $ticket->save();
        return back()->with('success', 'Ticket has been closed.');
    }     
    
    public function Blockuser($id)
    {
        $user=User::find($id);
        $user->status=1;
        $user->save();
        return back()->with('success', 'User has been suspended.');
    } 

    public function Unblockuser($id)
    {
        $user=User::find($id);
        $user->status=0;
        $user->save();
        return back()->with('success', 'User was successfully unblocked.');
    }    
    
    public function Readmessage($id)
    {
        $data=Contact::find($id);
        $data->seen=1;
        $data->save();
        return back()->with('success', 'Message has been marked read.');
    } 

    public function Unreadmessage($id)
    {
        $data=Contact::find($id);
        $data->seen=0;
        $data->save();
        return back()->with('success', 'Message has been marked unread.');
    }    
    
    public function Blockstaff($id)
    {
        $user=Admin::find($id);
        $user->status=1;
        $user->save();
        return back()->with('success', 'Staff has been suspended.');
    } 

    public function Unblockstaff($id)
    {
        $user=Admin::find($id);
        $user->status=0;
        $user->save();
        return back()->with('success', 'Staff was successfully unblocked.');
    }

    public function Approvekyc($id)
    {
        $set=Settings::first();
        $user=BUsiness::find($id);
        $user->kyc_status="APPROVED";
        $user->save();
        if($set->email_notify==1){
            dispatch(new approvedCompliance($user->receiver->id));
        }
        return back()->with('success', 'Compliance has been approved.');
    }    

    public function Rejectkyc($id)
    {
        $set=Settings::first();
        $user=Business::find($id);
        $user->kyc_status="DECLINED";
        $user->save();
        if($set->email_notify==1){
            dispatch(new rejectCompliance($user->receiver->id));
        }
        return back()->with('success', 'Compliance has been declined.');
    }    
    public function Resubmitkyc(Request $request,$id)
    {
        $set=Settings::first();
        $user=Business::find($id);
        $user->kyc_status="RESUBMIT";
        $user->save();
        if($set->email_notify==1){
            dispatch(new declinedCompliance($user->receiver->id, $request->reason));
        }
        return back()->with('success', 'Compliance has been declined.');
    }

    public function Profileupdate(Request $request, $id)
    {
        $data = User::findOrFail($id);
        $data->first_name=$request->first_name;
        $data->last_name=$request->last_name;
        if(empty($request->email_verify)){
            $data->email_verify=0;	
        }else{
            $data->email_verify=$request->email_verify;
        }             
        if(empty($request->fa_status)){
            $data->fa_status=0;	
        }else{
            $data->fa_status=$request->fa_status;
        }         
        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }    
    public function Staffupdate(Request $request)
    {
        $data = Admin::whereid($request->id)->first();
        $data->username=$request->username;
        $data->first_name=$request->first_name;
        $data->last_name=$request->last_name;
        if(empty($request->profile)){
            $data->profile=0;	
        }else{
            $data->profile=$request->profile;
        }  

        if(empty($request->support)){
            $data->support=0;	
        }else{
            $data->support=$request->support;
        }    

        if(empty($request->promo)){
            $data->promo=0;	
        }else{
            $data->promo=$request->promo;
        }     

        if(empty($request->message)){
            $data->message=0;	
        }else{
            $data->message=$request->message;
        }     

        if(empty($request->settlement)){
            $data->settlement=0;	
        }else{
            $data->settlement=$request->settlement;
        }     
        
        
        if(empty($request->country_supported)){
            $data->country_supported=0;	
        }else{
            $data->country_supported=$request->country_supported;
        }          
        
        if(empty($request->knowledge_base)){
            $data->knowledge_base=0;	
        }else{
            $data->knowledge_base=$request->knowledge_base;
        }          
        
        if(empty($request->language)){
            $data->language=0;	
        }else{
            $data->language=$request->language;
        }          
        
        if(empty($request->email_configuration)){
            $data->email_configuration=0;	
        }else{
            $data->email_configuration=$request->email_configuration;
        }     

        if(empty($request->general_settings)){
            $data->general_settings=0;	
        }else{
            $data->general_settings=$request->general_settings;
        }   

        if(empty($request->news)){
            $data->news=0;	
        }else{
            $data->news=$request->news;
        }         
        
        if(empty($request->payment)){
            $data->payment=0;	
        }else{
            $data->payment=$request->payment;
        }         
        
        if(empty($request->transactions)){
            $data->transactions=0;	
        }else{
            $data->transactions=$request->transactions;
        }          
        if(empty($request->vcard)){
            $data->vcard=0;	
        }else{
            $data->vcard=$request->vcard;
        }                  

        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }


    public function logout()
    {

        $set=Settings::find(1);
        if(Auth::guard('admin')->user()->id==1){
            $route=$set->admin_url;
        }else{
            $route="staff";
        }
        Auth::guard('admin')->logout();
        return redirect('/'.$route)->with('success', 'Just Logged Out!');
    }
        
}
