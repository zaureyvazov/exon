<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with(['doctor', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show payment form for a specific doctor.
     */
    public function create($doctorId)
    {
        $doctor = User::findOrFail($doctorId);

        if (!$doctor->isDoctor()) {
            return redirect()->route('admin.balances')->with('error', 'İstifadəçi doktor deyil');
        }

        $totalBalance = $doctor->getBalance();
        $totalPaid = $doctor->getTotalPaidAmount();
        $remainingBalance = $totalBalance - $totalPaid;

        return view('admin.payments.create', compact('doctor', 'totalBalance', 'totalPaid', 'remainingBalance'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
        ]);

        $doctor = User::findOrFail($request->doctor_id);
        $remainingBalance = $doctor->getRemainingBalance();

        if ($request->amount > $remainingBalance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ödəniş məbləği qalıq balansdan çox ola bilməz');
        }

        // Create payment
        $payment = Payment::create([
            'doctor_id' => $request->doctor_id,
            'admin_id' => Auth::id(),
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        // Create notification for doctor
        Notification::create([
            'user_id' => $request->doctor_id,
            'type' => 'payment',
            'title' => 'Yeni Ödəniş',
            'message' => number_format($request->amount, 2, '.', '') . ' AZN ödəniş sizə köçürüldü',
            'data' => [
                'payment_id' => $payment->id,
                'amount' => $request->amount,
            ],
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Ödəniş uğurla edildi');
    }

    /**
     * Show payment details.
     */
    public function show($id)
    {
        $payment = Payment::with(['doctor', 'admin'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }
}

