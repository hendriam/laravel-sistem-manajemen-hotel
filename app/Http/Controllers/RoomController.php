<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomType;

class RoomController extends Controller
{
    protected string $title = "Kamar";

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'room_number',
                'room_type.name',
                'floor.name',
                'price',
                'description',
                'status',
                'created_by',
                'created_at' 
            ];
            $totalData = Room::count();

            $limit = $request->input('length');   // jumlah data per halaman
            $start = $request->input('start');    // offset

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Room::with(['roomType', 'floor','createdBy', 'updatedby']);

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

        return view('room.index', ['title' => $this->title]);
    }

    public function create()
    {
        return view('room.create', [
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'room_number' => 'required|min:3|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor_id' => 'required',
            'price' => 'required|numeric|min:3',
            'description' => 'nullable|string',
            'status' => 'required|in:available,booked,occupied,cleaning',
        ]);

        try {
            Room::create($request->all() + ['created_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'redirect' => route('room.index')
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $data = Room::findOrFail($id);
        return view('room.edit', compact('data'), [
            'title' => $this->title,
            'roomTypes' => RoomType::all(),
            'floors' => Floor::all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        // Validasi
        $request->validate([
            'room_number' => 'required|min:3|unique:rooms,room_number,'.$id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor_id' => 'required|exists:floors,id',
            'price' => 'required|numeric|min:3',
            'description' => 'nullable|string',
            'status' => 'required|in:available,booked,occupied,cleaning',
        ]);

        try {
            $room = Room::findOrFail($id);
            if (!$room) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $room->update($request->all() + ['updated_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diubah.',
                'redirect' => route('room.index')
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
        $query = Room::with(['roomType', 'floor']);
        $query->where('status', 'available');
        
        $search = $request->q;
        return $query->where('room_number', 'like', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->room_number,
                    'text' => 'Kamar '. $room->room_number. ' ( ' . $room->roomType->name . ' - ' . $room->floor->name . ' )',
                ];
            });
    }

    public function getJson(string $id)
    {
        $data = Room::findOrFail($id);
         return response()->json([
            'success' => true,
            'room' => $data,
        ], 200);
    }
}
