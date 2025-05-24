<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileController extends Controller
{
    protected string $title = "Profile";
    protected static ?string $password;

    public function index() : View
    {
        return view('profile', ['title' => $this->title]);
    }

    public function changePassword(Request $request, string $id)
    {
        // Validasi awal
        $request->validate([
            'password' => 'required|string|min:8',
        ]);
       
        try {
            $user = User::findOrFail($id);

            if (!$user) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $user->update([
                'password' => static::$password ??= Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Password berhasil diganti!',
                'redirect' => route('profile')
            ], 200);

        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request, string $id)
    {
        // Validasi awal
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'username' => 'required|string|min:8|unique:users,username',
        ]);
       
        try {
            $user = User::findOrFail($id);

            if (!$user) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $user->update([
                'name' => $request->name,
                'username' => $request->username,
            ]);

            return response()->json([
                'message' => 'Profile berhasil diupdate!',
                'redirect' => route('profile')
            ], 200);

        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
