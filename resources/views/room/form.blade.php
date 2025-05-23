<div class="row">
	<div class="col-md-6">
		<div class="mb-2">
			<label for="room_number" class="form-label">Nomor Kamar</label>
			<input type="text" name="room_number" id="room_number" class="form-control" placeholder="Contoh: Kamar 101" value="{{ $data->room_number }}">
		</div>

		<div class="mb-2" id="roomType">
			<label for="room_type_id" class="form-label">Tipe Kamar</label>
			<select class="form-control select2bs44" name="room_type_id" id="room_type_id">
				@if($roomTypes)
					@foreach ($roomTypes as $roomType)
						<option value="{{ $roomType->id }}" {{ $data->room_type_id == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
					@endforeach
				@endif
			</select>
		</div>

		<div class="mb-2" id="floor">
			<label for="floor_id" class="form-label">Lantai</label>
			<select class="form-control select2bs4" name="floor_id" id="floor_id">
				@if($floors)
					@foreach ($floors as $floor)
						<option value="{{ $floor->id }}" {{ $data->floor_id == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
					@endforeach
				@endif
			</select>
		</div>

		<div class="mb-2">
			<label for="price" class="form-label">Harga Permalam</label>
			<input type="number" name="price" id="price" class="form-control" value="{{ $data->price }}" placeholder="Contoh: 150000">
		</div>
	</div>
	<div class="col-md-6">
		<div class="mb-2">
			<label for="status" class="form-label">Status</label>
			<select class="form-control" name="status" id="status">
				<option value="">-- Select status --</option>
				<option value="available" {{ $data->status == "available" ? "selected" : "" }}>Tersedia</option>
				<option value="booked" {{ $data->status == "booked" ? "selected" : "" }}>Dibooking</option>
				<option value="occupied" {{ $data->status == "occupied" ? "selected" : "" }}>Terisi</option>
				<option value="cleaning" {{ $data->status == "cleaning" ? "selected" : "" }}>Dibersihkan</option>
			</select>
		</div>

		<div class="mb-2">
			<label for="description" class="form-label">Keterangan</label>
			<textarea name="description" id="description" class="form-control" rows="3" placeholder="Contoh: Kamar 1 sedang diperbaiki">{{ $data->description }}</textarea>
		</div>
	</div>
</div>