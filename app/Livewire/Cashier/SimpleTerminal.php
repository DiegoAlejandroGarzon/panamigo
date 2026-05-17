<?php

namespace App\Livewire\Cashier;

use Livewire\Component;
use App\Models\Order;
use App\Models\Expense;

class SimpleTerminal extends Component
{
    public $fastAmount = '';
    public $shouldPrint = true;
    public $zDate = '';
    public $zTotal = 0;
    public $zCount = 0;

    // Pedidos / Gastos
    public $expenseAmount = '';
    public $expenseConcept = '';
    public $expenseCategory = 'Materia Prima';

    public function mount()
    {
        $this->zDate = date('Y-m-d');
    }

    public function generateRandomZData()
    {
        $this->zTotal = rand(310000, 350000);
        $this->zCount = rand(80, 95);
    }

    public function registerExpense()
    {
        $this->validate([
            'expenseAmount'   => 'required|numeric|min:1',
            'expenseConcept'  => 'required|string|max:255',
            'expenseCategory' => 'required|string',
        ]);

        Expense::create([
            'amount'   => $this->expenseAmount,
            'concept'  => $this->expenseConcept,
            'category' => $this->expenseCategory,
            'user_id'  => auth()->id(),
        ]);

        $this->expenseAmount  = '';
        $this->expenseConcept = '';
        $this->expenseCategory = 'Materia Prima';
        session()->flash('expense_message', 'Pedido registrado correctamente.');
    }

    public function render()
    {
        $recentSales = Order::where('customer_served_by', 'Caja Simple')
            ->where('status', 'paid')
            ->latest()
            ->take(5)
            ->get();

        $todayExpenses = Expense::whereDate('created_at', date('Y-m-d'))
            ->latest()
            ->get();

        return view('livewire.cashier.simple-terminal', [
            'recentSales'   => $recentSales,
            'todayExpenses' => $todayExpenses,
        ]);
    }

    public function togglePrint()
    {
        $this->shouldPrint = !$this->shouldPrint;
    }

    public function processFastSale()
    {
        $this->validate([
            'fastAmount' => 'required|numeric|min:0.01'
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $this->fastAmount,
            'status' => 'paid',
            'customer_served_by' => 'Caja Simple'
        ]);

        if ($this->shouldPrint) {
            $this->dispatch('print-ticket', order: $order->load('items.product'));
        }

        $this->fastAmount = '';
        session()->flash('message', 'Venta rápida por $' . $order->total . ' procesada exitosamente.');
    }

    public function loadRealZData()
    {
        $date = $this->zDate ?: date('Y-m-d');
        $orders = Order::whereDate('created_at', $date)
            ->where('status', 'paid')
            ->get();
        
        $this->zTotal = $orders->sum('total');
        $this->zCount = $orders->count();
    }

    public function printZReport()
    {
        $this->dispatch('print-z', data: [
            'date' => $this->zDate,
            'total' => $this->zTotal,
            'count' => $this->zCount,
        ]);
    }

    public function openDrawerOnly()
    {
        $this->dispatch('open-drawer');
    }
}
