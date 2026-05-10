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
            'weight' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
            'age' => ['required', 'integer'],
        ]);

        $user = User::find($sessionUser->id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->weight = $validated['weight'];
        $user->height = $validated['height'];
        $user->age = $validated['age'];
        $user->save();

        $request->session()->put('user', $user);

        return response()->json([
            'message' => 'Activity profile updated successfully!',
            'data' => [
                'weight' => $user->weight,
                'height' => $user->height,
                'age' => $user->age,
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
}
