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

		<!-- <div class="mb-2">
			<label for="check_in_date" class="form-label">Tgl. Check-In</label>
			<input type="text" id="check_in_date" name="check_in_date" value="{{ $data->check_in_date ?? '' }}" data-target="#checkInDate" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">
		</div> -->
		<!-- checkin default per tanggal hari ini -->
		<input type="hidden" id="check_in_date" name="check_in_date" value="{{ $data->check_in_date ?? '' }}" data-target="#checkInDate" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">


		<div class="mb-2">
			<label for="check_out_date" class="form-label">Tgl. Check-Out</label>
			<input type="text" id="check_out_date" name="check_out_date" value="{{ $data->check_out_date ?? '' }}" data-target="#checkOutDate" data-toggle="datetimepicker" class="form-control datetimepicker-input" placeholder="Contoh: 2025-05-14">
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

		@if($isCreate)
			<div class="mb-2">
				<label for="number_of_days" class="form-label">Jumlah Hari</label>
				<input type="text" name="number_of_days" id="number_of_days" class="form-control" value="1" readonly>
			</div>

			<div class="mb-2">
				<label for="notes" class="form-label">Catatan Reservasi  (optional)</label>
				<textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Masukkan catatan reservasi jika perlu">{{ $data->notes ?? '' }}</textarea>
			</div>
		@endif

	</div>
	<div class="col-md-6">
		@if($isCreate)
			
			<div class="mb-2">
				<label for="room_price" class="form-label">Harga Kamar</label>
				<input type="text" name="room_price" id="room_price" class="form-control" value="0" readonly>
			</div>

			<div class="mb-2">
				<label for="total_price" class="form-label">Total</label>
				<input type="text" name="total_price" id="total_price" class="form-control" value="0" readonly>
			</div>

			<div class="mb-2">
				<label for="down_payment" class="form-label">DP / Uang Muka (Rp) <span style="font-size: 11px; color:maroon;">minimal 25% dari total</span></label>
				<input type="text" name="mask_down_payment" id="mask_down_payment" class="form-control" placeholder="Contoh : 100000" min="5">
				<input type="hidden" name="down_payment" id="down_payment" class="form-control">
			</div>

			<div class="mb-2">
				<label for="down_payment_method" class="form-label">Metode Bayar</label>
				<select class="form-control" name="down_payment_method" id="down_payment_method">
					<option value="">-- Select metode pembayaran --</option>
					<option value="cash">Cash</option>
					<option value="transfer">Transfer</option>
				</select>
			</div>
		
			<div class="mb-2">
				<label for="notes_down_payment" class="form-label">Catatan DP / Uang Muka  (optional)</label>
				<textarea name="notes_down_payment" id="notes_down_payment" class="form-control" rows="3" placeholder="Masukkan catatan dp jika perlu"></textarea>
			</div>
		@else
			<div class="mb-2">
				<label for="notes" class="form-label">Catatan Reservasi  (optional)</label>
				<textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Masukkan catatan reservasi jika perlu">{{ $data->notes ?? '' }}</textarea>
			</div>
		@endif 
	</div>
</div>