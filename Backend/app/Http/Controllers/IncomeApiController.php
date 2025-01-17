<?php

namespace App\Http\Controllers;

use App\Repositories\Income\IncomeRepositoryInterface;
use Illuminate\Http\Request;
use Exception;
use App\Models\Income;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Income\StoreIncomeRequest;
use App\Http\Requests\Income\UpdateIncomeRequest;

class IncomeApiController extends Controller
{
    protected $incomeRepository;

    public function __construct(IncomeRepositoryInterface $incomeRepository)
    {
        $this->incomeRepository = $incomeRepository;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->get('search', '');  // Get the search query
            $user_id = auth::id(); // Lấy user_id từ request
            $incomes = Income::with('category') // Eager load 'category' để giảm truy vấn
            ->where('user_id', $user_id) // Chỉ lấy dữ liệu thuộc về người dùng hiện tại
            ->when($search, function ($query, $search) {
                // Thêm điều kiện tìm kiếm nếu từ khóa không rỗng
                $query->where(function ($query) use ($search) {
                    $query->where('source', 'LIKE', "%{$search}%")
                        ->orWhere('amount', 'LIKE', "%{$search}%")
                        ->orWhere('date', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            // Tìm kiếm trong tên danh mục liên kết
                            $query->where('name', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('date', direction: 'desc')
            ->paginate(5); //

        
            return response()->json([
                'success' => true,
                'data' => $incomes->items(),
                'pagination' => [
                    'current_page' => $incomes->currentPage(),
                    'per_page' => $incomes->perPage(),
                    'total_items' => $incomes->total(),
                    'total_pages' => $incomes->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

public function store(StoreIncomeRequest $request)
{
    try {
        // The request is automatically validated by StoreIncomeRequest
        $validated = $request->validated();

        // Add user_id to the validated data
        $validated['user_id'] = auth::id();

        // Create the income entry
        $income = $this->incomeRepository->create($validated);

        // Update statistics based on the income date
        $this->updateStatistics($validated['date'], $validated['user_id']);

        return response()->json([
            'success' => true,
            'data' => $income,
            'message' => 'Thêm income thành công'
        ], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function show(int $id)
    {
        try {
            $income = $this->incomeRepository->find($id);

            return response()->json(['success' => true, 'data' => $income], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

 


public function update(UpdateIncomeRequest $request, int $id)
{
    try {
        $validated = $request->validated();

        // Find the existing income record
        $income = $this->incomeRepository->find($id);
        $oldDate = $income->date;

        // Update the income
        $this->incomeRepository->update($id, $validated);

        // Handle statistics update based on the date change
        if ($oldDate == $request->date) {
            $this->updateStatistics($request->date, $request->user_id);
        } else {
            $this->updateStatistics2($oldDate, -$income->amount, $request->user_id); // Subtract from old date
            $this->updateStatistics($request->date, $request->user_id);
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật thu nhập bản thân thành công'], 200);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}



    public function destroy(int $id)
    {
        try {
            $income = $this->incomeRepository->find($id);
            $this->incomeRepository->delete($id);
            $this->updateStatistics2($income->date, -$income->amount, $income->user_id);

            return response()->json(['success' => true, 'message' => 'Xóa thu nhập bản thân thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function updateStatistics($date, $user_id)
    {
        // Tính tổng số tiền theo ngày, tháng và năm từ bảng giao dịch
        $totalAmount = DB::table('incomes')
            ->where('date', $date) // Lọc theo ngày cụ thể
            ->where('user_id', $user_id) // Lọc theo user_id
            ->sum('amount'); // Cộng tất cả số tiền của giao dịch trong ngày

        // Cập nhật hoặc thêm thống kê vào bảng thống kê
        DB::table('statistics')
            ->updateOrInsert(
                ['date' => $date, 'user_id' => $user_id], // Thêm user_id vào điều kiện
                [
                    'total_income' => $totalAmount,
                    'balance' => DB::raw($totalAmount . ' - IFNULL(total_expense, 0)'),
                ]
            );
    }
    private function updateStatistics2($date, $amount, $user_id)
    {
        // Cập nhật hoặc thêm thống kê vào bảng statistics
        DB::table('statistics')
            ->updateOrInsert(
                ['date' => $date, 'user_id' => $user_id], // Thêm user_id vào điều kiện
                [
                    'total_income' => DB::raw('IFNULL(total_income, 0) + ' . $amount),
                ]
            );
        $totalAmount = DB::table('incomes')
            ->where('date', $date) // Lọc theo ngày cụ thể
            ->where('user_id', $user_id) // Lọc theo user_id
            ->sum('amount'); // Cộng tất cả số tiền của giao dịch trong ngày

        DB::table('statistics')
            ->updateOrInsert(
                ['date' => $date, 'user_id' => $user_id],
                [
                    'balance' => DB::raw($totalAmount . ' - IFNULL(total_expense, 0)'),
                ]
            );
    }

    
}
