<?php

namespace App\Http\Controllers;

use App\Repositories\Reminder\ReminderRepositoryInterface;
use Illuminate\Http\Request;
use Exception;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    protected $reminderRepository;

    public function __construct(ReminderRepositoryInterface $reminderRepository)
    {
        $this->reminderRepository = $reminderRepository;
    }

    /**
     * Retrieves a list of reminders with search functionality and pagination
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $user_id = auth::id();
            $reminders = Reminder::where('user_id', $user_id)
                ->where(function ($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('due_date', 'LIKE', "%{$search}%");
                })
                ->orderByRaw('FIELD(title, ?) DESC', [$search])
                ->orderBy('due_date', 'asc')
                ->paginate(5);

            return response()->json([
                'success' => true,
                'data' => $reminders->items(),
                'pagination' => [
                    'current_page' => $reminders->currentPage(),
                    'per_page' => $reminders->perPage(),
                    'total_items' => $reminders->total(),
                    'total_pages' => $reminders->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieves a list of incomplete reminders for the authenticated user
     */
    public function getList()
    {
        try {
            $user_id = auth::id();
            $reminders = Reminder::where('user_id', $user_id)
                ->where('status', false)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $reminders,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new reminder
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
            ]);
            $user_id = auth::id();
            $validated['user_id'] = $user_id;
            $reminder = $this->reminderRepository->create($validated);

            return response()->json(['success' => true, 'data' => $reminder, 'message' => 'Reminder created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieves detailed information of a specific reminder
     */
    public function show(int $id)
    {
        try {
            $reminder = $this->reminderRepository->find($id);
            if (auth::id() == $reminder->user_id) {
                return response()->json(['success' => true, 'data' => $reminder], 200);
            } else {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Updates a reminder's information
     */
    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
                'status' => 'required',
            ]);
            $user_id = auth::id();
            $validated['user_id'] = $user_id;
            $reminder = $this->reminderRepository->find($id);
            if (auth::id() == $validated['user_id']) {
                $this->reminderRepository->update($id, $validated);
                return response()->json(['success' => true, 'message' => 'Reminder updated successfully'], 200);
            } else {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a reminder
     */
    public function destroy(int $id)
    {
        try {
            $reminder = $this->reminderRepository->find($id);
            if (auth::id() == $reminder->user_id) {
                $this->reminderRepository->delete($id);
                return response()->json(['success' => true, 'message' => 'Reminder deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
