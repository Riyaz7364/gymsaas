<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FinanceType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $gymId    = auth()->user()->gym_id;
        $expenses = Expense::where('gym_id', $gymId)->with('financeType')->latest('date')->paginate(20);
        $total    = Expense::where('gym_id', $gymId)->whereMonth('date', now()->month)->sum('amount');
        return view('expenses.index', compact('expenses', 'total'));
    }

    public function create()
    {
        $gymId        = auth()->user()->gym_id;
        $financeTypes = FinanceType::where('gym_id', $gymId)->orderBy('name')->get();
        return view('expenses.create', compact('financeTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:200',
            'amount'          => 'required|numeric|min:0',
            'date'            => 'required|date',
            'finance_type_id' => 'nullable|exists:finance_types,id',
            'description'     => 'nullable|string',
        ]);
        $data['gym_id']  = auth()->user()->gym_id;
        $data['added_by'] = auth()->id();
        Expense::create($data);
        return redirect(gym_route('gym.expenses.index'))->with('success', 'Expense recorded.');
    }

    public function show(string $id) { return redirect(gym_route('gym.expenses.index')); }

    public function edit(string $id)
    {
        $gymId        = auth()->user()->gym_id;
        $expense      = Expense::where('gym_id', $gymId)->findOrFail($id);
        $financeTypes = FinanceType::where('gym_id', $gymId)->orderBy('name')->get();
        return view('expenses.edit', compact('expense', 'financeTypes'));
    }

    public function update(Request $request, string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $expense = Expense::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'title'           => 'required|string|max:200',
            'amount'          => 'required|numeric|min:0',
            'date'            => 'required|date',
            'finance_type_id' => 'nullable|exists:finance_types,id',
            'description'     => 'nullable|string',
        ]);
        $expense->update($data);
        return redirect(gym_route('gym.expenses.index'))->with('success', 'Expense updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        Expense::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect(gym_route('gym.expenses.index'))->with('success', 'Expense deleted.');
    }
}