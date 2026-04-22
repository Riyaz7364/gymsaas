<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceType;
use Illuminate\Http\Request;

class FinanceTypeController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $types = FinanceType::where('gym_id', $gymId)
            ->withCount('expenses')
            ->orderBy('name')
            ->paginate(20);
        return view('finance-types.index', compact('types'));
    }

    public function create() { return view('finance-types.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);
        $data['gym_id'] = auth()->user()->gym_id;
        FinanceType::create($data);
        return redirect()->route('finance-types.index')->with('success', 'Finance type added.');
    }

    public function edit(FinanceType $financeType)
    {
        return view('finance-types.edit', compact('financeType'));
    }

    public function update(Request $request, FinanceType $financeType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);
        $financeType->update($data);
        return redirect()->route('finance-types.index')->with('success', 'Finance type updated.');
    }

    public function destroy(FinanceType $financeType)
    {
        $financeType->delete();
        return redirect()->route('finance-types.index')->with('success', 'Finance type deleted.');
    }
}
