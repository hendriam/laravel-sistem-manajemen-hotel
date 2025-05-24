<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuestController extends Controller
{
    protected string $title = "Data Tamu";

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'name',
                'identity_type',
                'identity_number',
                'phone',
                'email',
                'address',
                'created_by',
                'created_at' 
            ];
            $totalData = Guest::count();

            $limit = $request->input('length');   // jumlah data per halaman
            $start = $request->input('start');    // offset

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Guest::with(['createdBy', 'updatedby']);

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

        return view('guest.index', ['title' => $this->title]);
    }

    public function create()
    {
        return view('guest.create', [
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
         // Validasi
        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'identity_type' => 'required|in:KTP,SIM,PASPOR',
            'identity_number' => 'required|min:9|max:16|unique:guests',
            'phone' => 'required|min:7|max:13',
            'email' => 'nullable|email',
            'address' => 'required',
        ]);

        try {
            Guest::create($request->all() + ['created_by' => Auth::id()]);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'redirect' => route('guest.index')
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = Guest::findOrFail($id);
        return view('guest.edit', compact('data'), [
            'title' => $this->title,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'identity_type' => 'required|in:KTP,SIM,PASPOR',
            'identity_number' => 'required|min:9|max:16|unique:guests,identity_number,' . $id,
            'phone' => 'required|min:7|max:13',
            'email' => 'nullable|email',
            'address' => 'required',
        ]);

        try {
            $guest = Guest::findOrFail($id);
            if (!$guest) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $guest->update($request->all() + ['updated_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diubah.',
                'redirect' => route('guest.edit', $id)
            ], 200);
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        $search = $request->q;
        return Guest::where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'text' => $guest->name,
                ];
            });
    }
}
