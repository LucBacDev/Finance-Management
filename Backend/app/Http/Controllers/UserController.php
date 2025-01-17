<?php
namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

use Exception;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function register(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);

            $user = $this->userRepository->createUser($request->only(['name', 'email', 'password']));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);
        // return $token;   
        return response()->json(['token' => $token, 'id' => $user->id, 'name' => $user->name]);
    }
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email', // Check if email exists in the 'users' table
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'The email address is not registered.'], 400);
            }

            $token = Str::random(60);
            $existingReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if ($existingReset) {
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->update([
                        'token' => hash('sha256', $token),
                        'created_at' => now(),
                    ]);
            } else {
                DB::table('password_reset_tokens')->insert([
                    'email' => $request->email,
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                ]);
            }
            $resetLink = url("http://localhost:3000/newpassword?token={$token}");

            Mail::send('reset', ['link' => $resetLink], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Password Reset Request');
            });

            return response()->json(['message' => 'Reset link sent successfully. Please check your email.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $e) {

            return response()->json([
                'message' => 'Failed to send email. Please check your email configuration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function resetPassword(Request $request)
    {
        try {
            $hashedToken = hash('sha256', $request->token);

            $reset = DB::table('password_reset_tokens')
                ->where('token', $hashedToken)
                ->first();
            if ($reset) {

                $user = User::where('email', $reset->email)->first();
                $user->password = bcrypt($request->password); // You can use Hash::make() if preferred
                $user->save();

                DB::table('password_reset_tokens')->where('email', $reset->email)->delete();

                return response()->json(['message' => 'Password reset successfully.'], 200);
            } else {
                return response()->json(['error' => 'Invalid or expired token.'], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to reset password.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function delete($id)
    {
        try {
            $user = $this->userRepository->find($id);
            $user->tokens()->delete();
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully',
                // 'data' => new UserResource($user),  // Transform the user data into a response
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'User not found or error occurred.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
    public function getUser()
    {
        $user = Auth::user();
        return new UserResource($user);
    }
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {

        $data = $request->validate([
            'name' => 'string|max:50',
            'password' => 'required|min:6',
        ]);

        $user = Auth::user();

        // if (!Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'error' => 'The provided password does not match our records.'
        //     ], 400);
        // }
        $user->password = Hash::make($request->password);
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'status' => 'Password updated successfully.',
            'name' => $request->name
        ]);
    }

}
