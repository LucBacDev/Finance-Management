<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CateroryApiConntroller extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request)
{
    try {
        $user_id = auth::id(); // Lấy user ID từ người dùng đã đăng nhập
        $search = $request->get('search', ''); // Lấy từ khóa tìm kiếm

        // Truy vấn danh sách danh mục theo user_id và từ khóa tìm kiếm
        $categories = Category::where('user_id', $user_id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            })
            ->orderBy('name', 'asc') // Sắp xếp theo tên
            ->paginate(5); // Phân trang với 10 mục mỗi trang
        return response()->json([
            'success' => true,
            'data' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total_items' => $categories->total(),
                'total_pages' => $categories->lastPage(),
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
public function store(StoreCategoryRequest $request)
{
    try {
        // The request is automatically validated by StoreCategoryRequest
        $validated = $request->validated();

        $validated['user_id'] = auth::id();
        // Create the category
        $category = $this->categoryRepository->create($validated);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Thêm danh mục chi tiêu bản thân thành công'
        ], 201);
    } catch (Exception $e) {
        // Handle any other exceptions (server errors, etc.)
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function show(int $id)
    {
        try {
            $expense = $this->categoryRepository->find($id);

            return response()->json(['success' => true, 'data' => $expense], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

public function update(UpdateCategoryRequest $request, int $id)
{
    try {
        // The request is automatically validated by UpdateCategoryRequest
        $validated = $request->validated();
        // Update the category
        $this->categoryRepository->update($id, $validated);

        return response()->json(['success' => true, 'message' => 'Cập nhật danh mục chi tiêu bản thân thành công'], 200);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    public function destroy(int $id)
    {
        try {
            $this->categoryRepository->delete($id);

            return response()->json(['success' => true, 'message' => 'Xóa tiền đầu ra bản thân thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
