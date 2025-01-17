<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;
use App\Repositories\Statistic\StatisticRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Thêm dòng này
use Carbon\Carbon;

class StatisticController extends Controller
{
    protected $statisticRepo;

    /**
     * Constructor to inject the StatisticRepositoryInterface dependency
     *
     * @param StatisticRepositoryInterface $statisticRepo
     */
    public function __construct(StatisticRepositoryInterface $statisticRepo)
    {
        $this->blogRepo = $statisticRepo;
    }

    public function getIncomeSummaryByDate(Request $request)
    {
        try {
            $id = Auth::id(); // Thay vì lấy từ request, sử dụng Auth để lấy id người dùng hiện tại
            $year = $request->year;
            $month = $request->month;
            // Truy vấn dữ liệu dựa trên năm và tháng
            $summary = DB::table('incomes as i')
                ->join('categories as c', 'i.category_id', '=', 'c.id') // Liên kết bảng categories
                ->selectRaw('
                c.id AS category_id,
                c.name AS category_name,
                SUM(i.amount) AS total_amount
            ')
                ->where('i.user_id', $id) // Thêm lọc theo user_id
                ->whereYear('i.date', $year) // Lọc theo năm
                ->whereMonth('i.date', $month) // Lọc theo tháng
                ->groupBy('c.id', 'c.name') // Nhóm theo id và tên danh mục
                ->orderBy('c.name') // Sắp xếp theo tên danh mục
                ->get();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getExpenseSummaryByDate(Request $request)
    {
        try {
            $id = Auth::id(); // Thay vì lấy từ request, sử dụng Auth để lấy id người dùng hiện tại
            $year = $request->year;
            $month = $request->month;
            // Truy vấn dữ liệu dựa trên năm và tháng
            $summary = DB::table('expenses as i')
                ->join('categories as c', 'i.category_id', '=', 'c.id') // Liên kết bảng categories
                ->selectRaw('
                c.id AS category_id,
                c.name AS category_name,
                SUM(i.amount) AS total_amount
            ')
                ->where('i.user_id', $id) // Thêm lọc theo user_id
                ->whereYear('i.date', $year) // Lọc theo năm
                ->whereMonth('i.date', $month) // Lọc theo tháng
                ->groupBy('c.id', 'c.name') // Nhóm theo id và tên danh mục
                ->orderBy('c.name') // Sắp xếp theo tên danh mục
                ->get();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTotalIncomeByMonthYear(Request $request)
    {
        try {
            $id = Auth::id(); // Thay vì lấy từ request, sử dụng Auth để lấy id người dùng hiện tại
            $year = $request->year;
            $month = $request->month;
            // Tính tổng số tiền trực tiếp
            $totalIncome = DB::table('incomes')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('user_id', $id) // Lọc theo user_id
                ->sum('amount'); // Tính tổng trực tiếp

            return response()->json([
                'success' => true,
                'total_income' => $totalIncome
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalExpenseByMonthYear(Request $request)
    {
        try {
            $id = Auth::id(); // Thay vì lấy từ request, sử dụng Auth để lấy id người dùng hiện tại
            $year = $request->year;
            $month = $request->month;
            // Tính tổng số tiền trực tiếp
            $totalExpense = DB::table('expenses')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('user_id', $id) // Lọc theo user_id
                ->sum('amount'); // Tính tổng trực tiếp

            return response()->json([
                'success' => true,
                'total_expense' => $totalExpense
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatisticsForPeriod(Request $request)
    {
        $userId = Auth::id(); // Lấy ID người dùng hiện tại
        $year = $request->year;
        $month = $request->month;
    
        // Tính toán khoảng thời gian
        $startDate = Carbon::create($year, $month, 1)->subMonths(4)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
    
        // Lấy dữ liệu từ cơ sở dữ liệu trong khoảng thời gian
        $statistics = Statistic::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get(['date', 'total_expense', 'balance']);
    
        // Tổ chức lại dữ liệu theo tháng
        $groupedStatistics = $statistics->groupBy(function ($stat) {
            return Carbon::parse($stat->date)->format('m-Y'); // Nhóm theo 'mm-yyyy'
        });
    
        // Tạo mảng kết quả với các tháng trong khoảng thời gian
        $formattedStatistics = [];
        for ($i = -4; $i <= 0; $i++) {
            // Tính toán tháng và năm
            $currentMonth = Carbon::create($year, $month, 1)->addMonths($i);
            $monthYear = $currentMonth->format('m-Y');
    
            // Lấy dữ liệu nhóm cho tháng hiện tại
            $dataForMonth = $groupedStatistics->get($monthYear, collect());
    
            // Tính tổng chi phí và cân bằng cho tháng hiện tại
            $totalExpense = $dataForMonth->sum('total_expense');
            $balance = $dataForMonth->sum('balance');
    
            // Thêm vào mảng kết quả
            $formattedStatistics[] = [
                'month_year' => $monthYear,
                'total_expense' => $totalExpense,
                'balance' => $balance,
            ];
        }
    
        return response()->json([
            'status' => 'success',
            'data' => $formattedStatistics,
        ]);
    }
    public function getTotalDay(Request $request)
    {
        try {
            // Xác thực dữ liệu đầu vào
            $validated = $request->validate([
                'day' => 'required|integer',
                'month' => 'required|integer',
                'year' => 'required|integer',
            ]);
    
            // Lấy các tham số từ yêu cầu
            $userId = Auth::id(); // Sử dụng auth để lấy user id
            $day = $validated['day'];
            $month = $validated['month'];
            $year = $validated['year'];
    
            // Tạo ngày từ tham số đầu vào
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
    
            // Truy vấn bảng `statistics` để lấy dữ liệu
            $statistics = DB::table('statistics')
                ->select(DB::raw('SUM(total_income) as total_income, SUM(total_expense) as total_expense'))
                ->where('user_id', $userId)
                ->whereDate('date', $date)
                ->first();
    
            // Trả về kết quả
            return response()->json([
                'status' => 'success',
                'date' => $date,
                'total_income' => $statistics->total_income ?? 0,
                'total_expense' => $statistics->total_expense ?? 0,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Xử lý lỗi xác thực
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
            ], 400);
        }
    }
}
