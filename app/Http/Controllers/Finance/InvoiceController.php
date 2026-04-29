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
        $gym    = auth()->user()->gym;
        $invoices = Invoice::where('gym_id', $gym->id)->with('member')->latest()->paginate(20);
        return view('invoices.index', compact(['invoices', 'gym']));
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
        return redirect(gym_route('gym.invoices.index'))->with('success', 'Invoice created.');
    }

    public function show($gym, Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $gymId   = auth()->user()->gym_id;
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
        return redirect(gym_route('gym.invoices.index'))->with('success', 'Invoice updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        Invoice::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect(gym_route('gym.invoices.index'))->with('success', 'Invoice deleted.');
    }

    public function pdf(string $id)
    {
        $gymId = auth()->user()->gym_id;
        $invoice = Invoice::where('gym_id', $gymId)->with(['member', 'payments'])->findOrFail($id);

        $pdf = \PDF::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_no . '.pdf');
    }
}