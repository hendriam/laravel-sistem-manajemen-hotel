<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Reservation;
use App\Models\Room;

class ReservationController extends Controller
{
    protected string $title = "Reservasi";

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'guest.name',
                'room.name',
                'check_in_date',
                'check_out_date',
                'status',
                'notes',
                'created_by.name',
                'created_at' 
            ];
            $totalData = Reservation::count();

            $limit = $request->input('length');   // jumlah data per halaman
            $start = $request->input('start');    // offset

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Reservation::with(['guest', 'room', 'createdBy', 'updatedby']);

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

        return view('reservation.index', ['title' => $this->title]);
    }

    public function create()
    {
        return view('reservation.create', [
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
        ]);

        try {
            Reservation::create($request->all() + ['status' => 'booked', 'created_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'redirect' => route('reservation.create')
            ], 201);
        } catch (HttpResponseException $e) {
            throw $e;
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
        $data = Reservation::findOrFail($id);
        return view('reservation.edit', compact('data'), [
            'title' => $this->title,
            'guests' => Guest::all(),
            'rooms' => Room::where('status', 'available')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:booked,checked_in,cancelled,completed',
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            $reservation->update($request->all() + ['updated_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diubah.',
                'redirect' => route('reservation.index')
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

    public function checkIn(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            if ($reservation->status !== 'booked') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Reservasi tidak dalam status booked.",
                ], 400));
            }

            $reservation->update([
                'status' => 'checked_in',
                'updated_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'occupied']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di check-in.',
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

    public function checkOut(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            if ($reservation->status !== 'checked_in') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Reservasi tidak dalam status check-in.",
                ], 400));
            }

            $reservation->update([
                'status' => 'completed',
                'updated_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di check-out.',
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

    public function cancel(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Data tidak ditemukan.",
                ], 404));
            }

            if ($reservation->status === 'cancelled') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => "Reservasi sudah dalam status batal.",
                ], 400));
            }

            $reservation->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di batalkan.',
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

    public function createDirectCheckin()
    {
        return view('reservation.direct_checkin',[
            'title' => 'Buat Check-in Lansung'
        ]);
    }

    public function storeDirectCheckin(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_out_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        try {
            $reservation = Reservation::create($request->all() + [
                'check_in_date' => now()->toDateString(),
                'status' => 'checked_in',
                'created_by' => Auth::id()
            ]);

            $reservation->room->update(['status' => 'occupied']);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'redirect' => route('reservation.direct.create')
            ], 201);
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
