<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected static ?string $password;
    protected string $title = "User";

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'name',
                'phone_number', 
                'address',
                'created_at' 
            ];
            $totalData = User::count();

            $limit = $request->input('length');   // jumlah data per halaman
            $start = $request->input('start');    // offset

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = User::query();

            // Search filter
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $data = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

           return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => $totalFiltered,
                "data" => $data
            ]);
        }

        return view('user.index', ['title' => $this->title]);
    }

    public function create()
    {
        return view('user.create', ['title' => $this->title]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3',
            'username' => 'required|string|min:8|unique:users,username',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => static::$password ??= Hash::make('password'),
            'role' => $request->role,
        ]);

        return redirect()->route('user.create')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);
        return view('user.edit', compact('data'),  ['title' => $this->title]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'username' => 'required|string|min:8|unique:users,username',
            'role' => 'required|string',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        try {
             $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }
                        
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus karena masih terhubung dengan data lain.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
