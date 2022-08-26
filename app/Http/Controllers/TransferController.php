<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transactions;
use App\Models\Paymentlink;
use App\Models\Settings;
use App\Models\Balance;
use App\Jobs\SendWithdrawEmail;


class TransferController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function payment()
    {
        $data['title'] = "Payment";
        $data['links'] = Paymentlink::sortable()
            ->orderby('id', 'desc')
            ->wheremode(1)
            ->paginate(10);
        return view('admin.payment.index', $data);
    }
    public function index()
    {
        $data['title'] = "Transactions";
        $data['links'] = Transactions::sortable()
            ->orderby('id', 'desc')
            ->wheremode(1)
            ->where('type', '!=', 3)
            ->paginate(10);
        return view('admin.payment.transactions', $data);
    }
    public function payout()
    {
        $data['title'] = "Transactions";
        $data['links'] = Transactions::sortable()
            ->orderby('id', 'desc')
            ->wheremode(1)
            ->wheretype(3)
            ->paginate(10);
        return view('admin.payment.transactions', $data);
    }
    public function searchTransaction(Request $request)
    {
        $data['title'] = "Transactions";
        $data['links'] = Transactions::where('amount', 'LIKE', '%' . $request->search . '%')
            ->orwhere('email', 'LIKE', '%' . $request->search . '%')
            ->orwhere('ref_id', 'LIKE', '%' . $request->search . '%')
            ->wheremode(1)
            ->where('type', '!=', 3)
            ->orderby('created_at', 'desc')->paginate(10);
        return view('admin.payment.transactions', $data);
    }
    public function searchPayment(Request $request)
    {
        $data['title'] = "Payment";
        $data['links'] = Paymentlink::where('amount', 'LIKE', '%' . $request->search . '%')
            ->orwhere('name', 'LIKE', '%' . $request->search . '%')
            ->orwhere('amount', 'LIKE', '%' . $request->search . '%')
            ->orwhere('description', 'LIKE', '%' . $request->search . '%')
            ->wheremode(1)
            ->orderby('created_at', 'desc')->paginate(10);
        return view('admin.payment.index', $data);
    }
    public function linkstrans($id)
    {
        $data['title'] = "Transactions";
        $data['links'] = Transactions::sortable()
            ->wherepayment_link($id)
            ->orderby('id', 'desc')
            ->wheremode(1)
            ->paginate(10);
        return view('admin.payment.transactions', $data);
    }
    public function unlinks($id)
    {
        $page = Paymentlink::find($id);
        $page->status = 0;
        $page->save();
        return back()->with('success', 'Payment link has been unsuspended.');
    }
    public function approvePayout($id)
    {
        $data = Transactions::whereref_id($id)->first();
        $data->status = 1;
        $data->save();
        if ($this->settings->email_notify == 1) {
            dispatch(new SendWithdrawEmail($data->ref_id));
        }
        return back()->with('success', 'Payout Approved.');
    }
    public function declinePayout(Request $request, $id)
    {
        $data = Transactions::whereref_id($id)->first();
        $data->status = 2;
        $data->save();
        //Balance
        $balance = Balance::whereuser_id($data->receiver_id)->wherebusiness_id($data->business_id)->wherecountry_id($data->currency)->first();
        $balance->amount = $balance->amount + $request->amount + $request->charge;
        $balance->save();
        if ($this->settings->email_notify == 1) {
            dispatch(new SendWithdrawEmail($data->ref_id, $request->reason));
        }
        return back()->with('success', 'Payout Declined.');
    }
    public function plinks($id)
    {
        $page = Paymentlink::find($id);
        $page->status = 1;
        $page->save();
        return back()->with('success', 'Payment has been suspended.');
    }
    public function viewTransactions($id, $type)
    {
        $data['val'] = Transactions::whereref_id($id)->first();
        if ($data['val']->type == 1) {
            $tt = "Payment";
        } elseif ($data['val']->type == 2) {
            $tt = "API";
        } elseif ($data['val']->type == 3) {
            $tt = "Payout";
        }elseif ($data['val']->type == 4) {
            $tt = "Funding";
        }
        $data['title'] = $tt;
        $data['type'] = $type;
        return view('admin.transactions.view', $data);
    }
    public function Destroylink($id)
    {
        $link = Paymentlink::whereid($id)->first();
        Transactions::wherepayment_link($id)->delete();
        $data = $link->delete();
        if ($data) {
            return back()->with('success', 'Payment link was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Payment link');
        }
    }
}
