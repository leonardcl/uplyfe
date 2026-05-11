<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate up-front so we redirect back to the form with friendly
        // errors instead of letting a DB UNIQUE constraint blow up the page.
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.unique' => 'An account with this email already exists. Try logging in instead.',
            'password.confirmed' => 'Password and confirmation do not match.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
        ]);

        return redirect('/login')->with('success', 'Account created! You can now log in.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sessionUser = $request->session()->get('user');

        if (!$sessionUser || !isset($sessionUser->id)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $sessionUser->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            // `age` is what the activity-profile modal posts (separate column from
            // date_of_birth). Validate it explicitly so the value isn't silently
            // dropped by Laravel's allow-list validator.
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'height' => ['nullable', 'numeric'],
            'weight' => ['nullable', 'numeric'],
            'dietary_preferences' => ['nullable', 'array'],
            'notification_preferences' => ['nullable', 'array'],
        ]);

        $user = User::find($sessionUser->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->first_name = $validated['first_name'] ?? $user->first_name;
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->email = $validated['email'] ?? $user->email;
        $user->phone_number = $validated['phone_number'] ?? $user->phone_number;
        $user->date_of_birth = $validated['date_of_birth'] ?? $user->date_of_birth;
        $user->gender = $validated['gender'] ?? $user->gender;
        $user->age = $validated['age'] ?? $user->age;
        $user->height = $validated['height'] ?? $user->height;
        $user->weight = $validated['weight'] ?? $user->weight;
        $user->dietary_preferences = $validated['dietary_preferences'] ?? $user->dietary_preferences;
        $user->notification_preferences = $validated['notification_preferences'] ?? $user->notification_preferences;

        $user->save();

        $request->session()->put('user', $user);

        return response()->json([
            'message' => 'Profile updated successfully!',
            'data' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'date_of_birth' => $user->date_of_birth,
                'gender' => $user->gender,
                'height' => $user->height,
                'weight' => $user->weight,
                'dietary_preferences' => $user->dietary_preferences,
                'notification_preferences' => $user->notification_preferences,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updatePassword(Request $request)
    {
        $sessionUser = $request->session()->get('user');

        if (!$sessionUser || !isset($sessionUser->id)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::find($sessionUser->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->password_changed_at = now();
        $user->save();

        $request->session()->put('user', $user);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }
}
