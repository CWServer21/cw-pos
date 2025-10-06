<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
	public function index()
	{
		if(Auth::user()->role_id <= 2) {
			$lims_cash_register_all = CashRegister::with('user', 'warehouse')->get();
			return view('backend.cash_register.index', compact('lims_cash_register_all'));
		}
		else
			return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
	}
	public function store(Request $request)
	{
		$data = $request->all();
		$data['status'] = true;
		$data['user_id'] = Auth::id();
		CashRegister::create($data);
		return redirect()->back()->with('message', __('db.Cash register created successfully'));
	}

	public function getDetails($id)
	{
		$cash_register_data = CashRegister::find($id);

		$data['cash_in_hand'] = $cash_register_data->cash_in_hand;
		$data['total_sale_amount'] = Sale::where([
										['cash_register_id', $cash_register_data->id],
										['sale_status', 1]
									])->sum(DB::raw('grand_total / exchange_rate'));
		$data['total_payment'] = Payment::where('cash_register_id', $cash_register_data->id)
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['cash_payment'] = Payment::where([
									['cash_register_id', $cash_register_data->id],
									['paying_method', 'Cash']
								])
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['credit_card_payment'] = Payment::where([
									['cash_register_id', $cash_register_data->id],
									['paying_method', 'Credit Card']
								])
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['gift_card_payment'] = Payment::where([
									['cash_register_id', $cash_register_data->id],
									['paying_method', 'Gift Card']
								])
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['deposit_payment'] = Payment::where([
									['cash_register_id', $cash_register_data->id],
									['paying_method', 'Deposit']
								])
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['cheque_payment'] = Payment::where([
									['cash_register_id', $cash_register_data->id],
									['paying_method', 'Cheque']
								])
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['paypal_payment'] = Payment::where([
									['cash_register_id', $cash_register_data->id],
									['paying_method', 'Paypal']
								])
								->whereNotNull('sale_id')
								->sum(DB::raw('amount / exchange_rate'));
		$data['total_supplier_payment'] = Payment::where('cash_register_id', $cash_register_data->id)
										->whereNotNull('purchase_id')
										->sum(DB::raw('amount / exchange_rate'));
		$data['total_sale_return'] = Returns::where('cash_register_id', $cash_register_data->id)->sum(DB::raw('grand_total / exchange_rate'));
		$data['total_expense'] = Expense::where('cash_register_id', $cash_register_data->id)->sum('amount');
		$data['total_cash'] = $data['cash_in_hand'] + $data['total_payment'] - ($data['total_sale_return'] + $data['total_expense'] + $data['total_supplier_payment']);
		$data['status'] = $cash_register_data->status;
		return $data;
	}

	public function close(Request $request)
	{
		$cash_register_data = CashRegister::find($request->cash_register_id);
		$cash_register_data->closing_balance = $request->closing_balance;
		$cash_register_data->actual_cash = $request->actual_cash;
		$cash_register_data->status = 0;
		$cash_register_data->save();
		return redirect()->back()->with('message', __('db.Cash register closed successfully'));
	}

    public function checkAvailability($warehouse_id)
    {
    	$open_register_number = CashRegister::select('id')->where([
						    		['user_id', Auth::id()],
						    		['warehouse_id', $warehouse_id],
						    		['status', true]
						    	])->first();
    	if($open_register_number)
    		return $open_register_number->id;
    	else
    		return 'false';
    }
}
