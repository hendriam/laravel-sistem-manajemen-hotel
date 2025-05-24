<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RoomType;

class RoomTypeController extends Controller
{
    protected string $title = "Tipe Kamar";

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'name',
                'description',
                'created_by',
                'created_at' 
            ];
            $totalData = RoomType::count();

            $limit = $request->input('length');   // jumlah data per halaman
            $start = $request->input('start');    // offset

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = RoomType::with(['createdBy', 'updatedby']);

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

        return view('room_types.index', ['title' => $this->title]);
    }

    public function create()
    {
        return view('room_types.create', [
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
        ]);
        
        RoomType::create($request->all() + ['created_by' => Auth::id()]);

        return redirect()->route('room-types.create')->with('success', 'Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = RoomType::findOrFail($id);
        return view('room_types.edit', ['title' => $this->title, 'data' => $data]);
    }

    public function update(Request $request, string $id)
    {
        $roomType = RoomType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
        ]);

        $roomType->update($request->all() + ['updated_by' => Auth::id()]);

        return redirect()->route('room-types.edit', $id)->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        try {
             $roomType = RoomType::find($id);

            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }
                        
            $roomType->delete();

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

    public function search(Request $request)
    {
        $search = $request->q;
        return RoomType::where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($roomType) {
                return [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                    'text' => $roomType->name,
                ];
            });
    }
}
