<?php

namespace App\Http\Controllers;

use App\Repositories\Expense\ExpenseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;

class ExpenseApiController extends Controller
{
    protected $expenseRepository;

    public function __construct(ExpenseRepositoryInterface $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $user_id = auth::id(); // Lấy user_id từ request

            $expenses = Expense::with('category') // Eager load 'category' to reduce queries
                ->where('user_id', $user_id) // Only get expenses belonging to the current user
                ->when($search, function ($query, $search) {
                    // Add search conditions if search keyword is provided
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('amount', 'LIKE', "%{$search}%")
                            ->orWhere('date', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%")
                            ->orWhereHas('category', function ($query) use ($search) {
                                // Search in related category name
                                $query->where('name', 'LIKE', "%{$search}%");
                            });
                    });
                })
                ->orderBy('date', direction: 'desc')
                ->paginate(5);

            return response()->json([
                'success' => true,
                'data' => $expenses->items(),
                'pagination' => [
                    'current_page' => $expenses->currentPage(),
                    'per_page' => $expenses->perPage(),
                    'total_items' => $expenses->total(),
                    'total_pages' => $expenses->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

public function store(StoreExpenseRequest $request)
{
    try {
        // The request is automatically validated by StoreExpenseRequest
        $validated = $request->validated();

        // Add user_id to the validated data
        $validated['user_id'] = auth::id();

        // Create the expense
        $expense = $this->expenseRepository->create($validated);

        // Update statistics
        $this->updateStatistics($validated['date'], $validated['user_id']);

        return response()->json([
            'success' => true,
            'data' => $expense,
            'message' => 'Thêm tiền đầu ra thành công'
        ], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function show(int $id)
    {
        try {
            $expense = $this->expenseRepository->find($id);

            return response()->json(['success' => true, 'data' => $expense], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

 

public function update(UpdateExpenseRequest $request, int $id)
{
    try {
        // The request is automatically validated by UpdateExpenseRequest
        $validated = $request->validated();

        // Find the existing expense
        $expense = $this->expenseRepository->find($id);
        $oldDate = $expense->date;

        // Update the expense
        $this->expenseRepository->update($id, $validated);

        // Handle statistics update based on the date change
        if ($oldDate == $request->date) {
            $this->updateStatistics($request->date, $request->user_id);
        } else {
            $this->updateStatistics2($oldDate, -$expense->amount, $request->user_id);
            $this->updateStatistics($request->date, $request->user_id);
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật tiền đầu ra thành công'], 200);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    public function destroy(int $id)
    {
        try {
            $expense = $this->expenseRepository->find($id)
            ->where('user_id', auth::id())
            ;
            $this->expenseRepository->delete($id);
            $this->updateStatistics2($expense->date, -$expense->amount, $expense->user_id);

            return response()->json(['success' => true, 'message' => 'Xóa tiền đầu ra thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function updateStatistics($date, $user_id)
    {
        $totalAmount = DB::table('expenses')
            ->where('date', $date)
            ->where('user_id', $user_id)
            ->sum('amount');

        DB::table('statistics')
            ->updateOrInsert(
                ['date' => $date, 'user_id' => $user_id],
                [
                    'total_expense' => $totalAmount,
                    'balance' => DB::raw('IFNULL(total_income, 0) - ' . $totalAmount),
                ]
            );
    }

    private function updateStatistics2($date, $amount, $user_id)
    {
        DB::table('statistics')
            ->updateOrInsert(
                ['date' => $date, 'user_id' => $user_id],
                [
                    'total_expense' => DB::raw('IFNULL(total_expense, 0) + ' . $amount),
                ]
            );

        $totalAmount = DB::table('expenses')
            ->where('date', $date)
            ->where('user_id', $user_id)
            ->sum('amount');

        DB::table('statistics')
            ->updateOrInsert(
                ['date' => $date, 'user_id' => $user_id],
                [
                    'balance' => DB::raw('IFNULL(total_income, 0) - ' . $totalAmount),
                ]
            );
    }
}
