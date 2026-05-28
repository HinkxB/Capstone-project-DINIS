<?php

namespace App\Http\Controllers;

// Import the base Controller and other necessary classes
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    // Fetch all users
    public function index() {
        return response()->json(User::all());
    }

    // Create a new system user
    public function store(Request $request) {
        try {
            // Validate incoming request for better error reporting
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'username'  => 'required|string|unique:users,username',
                'password'  => 'required|string|min:6',
                'role'      => 'required|string',
                'department'=> 'nullable|string'
            ]);

            $user = User::create([
                'full_name' => $validated['full_name'],
                'username'  => $validated['username'],
                'password'  => Hash::make($validated['password']),
                'role'      => $validated['role'],
                'department'=> $validated['department'],
            ]);

            return response()->json(['success' => true, 'user' => $user], 201);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database Error: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }
}