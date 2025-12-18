<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of transactions
     */
    public function index(Request $request, Congress $congress = null)
    {
        // Solo admins pueden ver transacciones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $query = Transaction::with(['payment.user', 'congress']);

        if ($congress) {
            $query->where('congress_id', $congress->id);
        }

        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);

        $totalDebits = Transaction::where('type', 'debit')
            ->when($congress, fn($q) => $q->where('congress_id', $congress->id))
            ->sum('amount');

        $totalCredits = Transaction::where('type', 'credit')
            ->when($congress, fn($q) => $q->where('congress_id', $congress->id))
            ->sum('amount');

        $balance = $totalDebits - $totalCredits;

        return view('admin.transactions.index', compact('transactions', 'congress', 'totalDebits', 'totalCredits', 'balance'));
    }

    /**
     * Export transactions to Excel
     */
    public function export(Request $request, Congress $congress = null)
    {
        // Solo admins pueden exportar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $query = Transaction::with(['payment.user', 'congress']);

        if ($congress) {
            $query->where('congress_id', $congress->id);
        }

        // Aplicar mismos filtros que en index
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $export = new class($transactions) implements FromCollection, WithHeadings, WithMapping {
            public function __construct(public $transactions) {}

            public function collection()
            {
                return $this->transactions;
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Fecha',
                    'Tipo',
                    'Monto',
                    'Moneda',
                    'Descripción',
                    'Congreso',
                    'Usuario',
                    'Referencia',
                ];
            }

            public function map($transaction): array
            {
                return [
                    $transaction->id,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->type === 'debit' ? 'Débito' : 'Crédito',
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->description,
                    $transaction->congress?->title ?? 'N/A',
                    $transaction->payment?->user?->name ?? 'N/A',
                    $transaction->reference ?? 'N/A',
                ];
            }
        };

        $filename = 'transacciones_' . ($congress ? $congress->slug : 'todos') . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download($export, $filename);
    }
}
