<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MetaMaskController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'address' => 'required|string'
        ]);

        $address = $request->address;

        // Check if user exists, otherwise create one
        $user = User::where('wallet_address', $address)->first();

        if (!$user) {
            $user = User::create([
                'wallet_address' => $address,
                'name' => 'MetaMask User'
            ]);
        }

        // Log the user in
        Auth::login($user);

        return response()->json(['success' => true, 'user' => $user]);
    }
}
