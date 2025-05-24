<div class="row">
	<div class="col-md-6">
		<div class="mb-2" id="roomType">
			<label for="guest_id" class="form-label">Nama Tamu</label>
			<select class="form-control select2bs44" name="guest_id" id="guest_id">
				@if($guests)
					@foreach ($guests as $guest)
						<option value="{{ $guest->id }}" {{ ($data->guest_id ?? '') == $guest->id ? 'selected' : '' }}>{{ $guest->name }}</option>
					@endforeach
				@endif
			</select>
		</div>

		<div class="mb-2" id="floor">
			<label for="room_id" class="form-label">Kamar</label>
			<select class="form-control select2bs4" name="room_id" id="room_id">
				@if($rooms)
					@foreach ($rooms as $room)
						<option value="{{ $room->id }}" {{ ($data->room_id ?? '') == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
					@endforeach
				@endif
			</select>
		</div>

		<div class="mb-2">
			<label for="check_in_date" class="form-label">Tgl. Check-In</label>
			<input type="text" id="check_in_date" name="check_in_date" value="{{ $data->check_in_date ?? '' }}" data-target="#checkInDate" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">
		</div>

		<div class="mb-2">
			<label for="check_out_date" class="form-label">Tgl. Check-Out</label>
			<input type="text" id="check_out_date" name="check_out_date" value="{{ $data->check_out_date ?? '' }}" data-target="#checkOutDate" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">
		</div>
	</div>
	<div class="col-md-6">
		<!-- @if($data)
			<div class="mb-2">
				<label for="status" class="form-label">Status</label>
				<select class="form-control" name="status" id="status">
					<option value="">-- Select status --</option>
					<option value="booked" {{ ($data->status ?? '') == "booked" ? "selected" : "" }}>Dibooking</option>
					<option value="checked_in" {{ ($data->status ?? '') == "checked_in" ? "selected" : "" }}>Check in</option>
					<option value="completed" {{ ($data->status ?? '') == "completed" ? "selected" : "" }}>Check out</option>
					<option value="cancelled" {{ ($data->status ?? '') == "cancelled" ? "selected" : "" }}>Batal</option>
				</select>
			</div>
		@endif -->
	
		<div class="mb-2">
			<label for="notes" class="form-label">Catatan</label>
			<textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Masukkan catatan jika perlu (optional)">{{ $data->notes ?? '' }}</textarea>
		</div>
	</div>
</div>