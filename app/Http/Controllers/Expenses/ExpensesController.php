<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    //use ValidatesTransactions;
    // create this as a request object

    public function index()
    {
        $admission = $this->loadAdmissionAndSharePagination();
        $transactions = $admission->patient->expenses;
        $expenseTotals = [
            'totalDebits' => $transactions->totalDebits(true),
            'totalCredits' => $transactions->totalCredits(true),
            'costOfCare' => $transactions->costOfCare(true),
        ];
        $categories = Category::whereNull('parent_id')->whereNull('account_id')->with(['children' => function ($query) {
            $query->where('account_id', Auth::user()->current_team_id)->orderBy('name');
        }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                if ($category->children->isNotEmpty()) {
                    return $category->children
                        ->pluck('name')
                        ->map(fn ($name) => "{$category->name}:{$name}")
                        ->prepend($category->name)
                        ->all();
                }

                return $category->name;
            })
            ->flatten()
            ->map(function ($name) {
                return [
                    'label' => $name,
                    'value' => Str::after($name, ':'),
                ];
            })
            ->toArray();

        OptionsStore::merge(compact('categories'));
        ExtensionNavigation::emit('patient', $admission);

        return Inertia::render('Patients/Expenses/Index', compact('transactions', 'expenseTotals'));
    }

    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $this->validateTransactionRequest($request, Auth::user()->current_team_id);

        tap(new Transaction($request->only(['transacted_at', 'debit', 'credit', 'memo'])), function ($transaction) use ($patient, $request) {
            $transaction->patient_id = $patient->id;
            $transaction->category_id = Category::findByName($request->input('category'), Auth::user()->currentAccount)->id;
            $transaction->save();
        });

        $admission = Admission::custody(Auth::user()->currentAccount, $patient);

        return redirect()
            ->route('patients.expenses.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303);
    }

    public function update(Request $request, Patient $patient, Transaction $transaction)
    {
        $transaction->validateOwnership(Auth::user()->current_team_id);

        $this->validateTransactionRequest($request, Auth::user()->current_team_id);

        $transaction->update($request->only(['transacted_at', 'debit', 'credit', 'memo']));

        $transaction->category_id = Category::findByName($request->input('category'), Auth::user()->currentAccount)->id;
        $transaction->save();

        $admission = Admission::custody(Auth::user()->currentAccount, $patient);

        return redirect()
            ->route('patients.expenses.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303);
    }

    public function destroy(Request $request, Patient $patient, Transaction $transaction)
    {
        $transaction->validateOwnership(Auth::user()->current_team_id)->delete();

        $admission = Admission::custody(Auth::user()->currentAccount, $patient);

        return redirect()
            ->route('patients.expenses.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303)
            ->with('flash.notificationHeading', 'Transaction Deleted')
            ->with('flash.notification', "{$transaction->category->name} transaction on $transaction->transacted_at_for_humans was deleted.");
    }
}
