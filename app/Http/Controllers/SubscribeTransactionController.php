<?php

namespace App\Http\Controllers;

use App\Models\SubscribeTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscribeTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This function retrieves all subscription transactions from the database, including 
     * associated user information. The transactions are ordered in descending order by 
     * their ID. The data is then passed to the view to be displayed.
     *
     * @return \Illuminate\View\View The view displaying the list of subscription transactions.
     */
    public function index()
    {
        // Retrieve all subscription transactions with user data and order by ID in descending order
        $transactions = SubscribeTransaction::with(['user'])->orderByDesc('id')->get();

        // Return the view with the list of transactions
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * This function displays the details of a specific subscription transaction. It 
     * retrieves the transaction from the database using the provided model instance 
     * and passes it to the view for display.
     *
     * @param \App\Models\SubscribeTransaction $subscribeTransaction The subscription transaction to be displayed.
     * @return \Illuminate\View\View The view displaying the details of the specified subscription transaction.
     */
    public function show(SubscribeTransaction $subscribeTransaction)
    {
        // Return the view with the details of the specified subscription transaction
        return view('admin.transactions.show', compact('subscribeTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscribeTransaction $subscribeTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * This function updates the specified subscription transaction to mark it as paid and sets the subscription 
     * start date to the current date. The update operation is performed within a database transaction to ensure 
     * data consistency.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request object containing any necessary data for updating.
     * @param \App\Models\SubscribeTransaction $subscribeTransaction The subscription transaction to be updated.
     * @return \Illuminate\Http\RedirectResponse A redirect to the show page for the updated subscription transaction.
     */
    public function update(Request $request, SubscribeTransaction $subscribeTransaction)
    {
        // Perform the update operation within a database transaction
        DB::transaction(function () use ($subscribeTransaction) {

            // Update the subscription transaction with new data
            $subscribeTransaction->update([
                'is_paid' => true,
                'subscription_start_date' => Carbon::now(),
            ]);

        });

        // Redirect to the show page for the updated subscription transaction
        return redirect()->route('admin.subscribe_transactions.show', $subscribeTransaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscribeTransaction $subscribeTransaction)
    {
        //
    }
}
