<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveExpenseTransactionRequest;
use App\Models\Admission;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

        $expenseCategories = ExpenseCategory::whereNull('parent_id')->whereNull('team_id')->with(['children' => function ($query) {
            $query->where('team_id', Auth::user()->current_team_id)->orderBy('name');
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

        OptionsStore::add(compact('expenseCategories'));

        return Inertia::render('Patients/Expenses/Index', [
            'patient' => $admission->patient,
            'expenseTransactions' => $admission->patient->expenseTransactions,
            'expenseTotals' => $expenseTotals,
        ]);
    }

    public function store(SaveExpenseTransactionRequest $request, Patient $patient)
    {
        $transaction = new ExpenseTransaction($request->only(['transacted_at', 'debit', 'credit', 'memo']));
        $transaction->patient_id = $patient->id;
        $transaction->category_id = ExpenseCategory::findByName($request->input('category'), Auth::user()->currentTeam)->id;
        $transaction->save();

        $admission = Admission::custody(Auth::user()->currentTeam, $patient);

        return redirect()
            ->route('patients.expenses.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303);
    }

    public function update(SaveExpenseTransactionRequest $request, Patient $patient, ExpenseTransaction $transaction)
    {
        $transaction->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient);

        $transaction->update($request->only(['transacted_at', 'debit', 'credit', 'memo']));
        $transaction->category_id = ExpenseCategory::findByName($request->input('category'), Auth::user()->currentTeam)->id;
        $transaction->save();

        $admission = Admission::custody(Auth::user()->currentTeam, $patient);

        return redirect()
            ->route('patients.expenses.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303);
    }

    public function destroy(Request $request, Patient $patient, Transaction $transaction)
    {
        $transaction->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient)
            ->delete();

        $admission = Admission::custody(Auth::user()->currentTeam, $patient);

        return redirect()
            ->route('patients.expenses.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303)
            ->with('notification.heading', __('Transaction Deleted'))
            ->with('notification.text', __(':categoryName transaction on :transactionDate was deleted.', [
                'categoryName' => $transaction->category->name,
                'transactionDate' => $transaction->transacted_at_for_humans,
            ]));
    }
}
