<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Member;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $gymId    = auth()->user()->gym_id;
        $invoices = Invoice::where('gym_id', $gymId)->with('member')->latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('invoices.create', compact('members'));
    }

    public function store(Request $request)
    {
        $gymId = auth()->user()->gym_id;
        $data = $request->validate([
            'member_id'  => 'required|exists:members,id',
            'subtotal'   => 'required|numeric|min:0',
            'tax'        => 'nullable|numeric|min:0',
            'discount'   => 'nullable|numeric|min:0',
            'due_date'   => 'nullable|date',
            'notes'      => 'nullable|string',
        ]);
        $data['gym_id']     = $gymId;
        $data['invoice_no'] = 'INV-' . strtoupper(uniqid());
        $data['total']      = ($data['subtotal'] ?? 0) + ($data['tax'] ?? 0) - ($data['discount'] ?? 0);
        $data['balance_due'] = $data['total'];
        $data['status']     = 'unpaid';
        Invoice::create($data);
        return redirect()->route('invoices.index')->with('success', 'Invoice created.');
    }

    public function show(string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $invoice = Invoice::where('gym_id', $gymId)->with(['member', 'payments'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $invoice = Invoice::where('gym_id', $gymId)->findOrFail($id);
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('invoices.edit', compact('invoice', 'members'));
    }

    public function update(Request $request, string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $invoice = Invoice::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'status'   => 'required|in:unpaid,partial,paid,cancelled',
            'due_date' => 'nullable|date',
            'notes'    => 'nullable|string',
        ]);
        $invoice->update($data);
        return redirect()->route('invoices.index')->with('success', 'Invoice updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        Invoice::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}