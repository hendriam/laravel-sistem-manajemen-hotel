<div class="row">
	<div class="col-md-6">
		<div class="mb-2">
			<label for="name" class="form-label">Nama Tamu</label>
			<input type="text" name="name" id="name" class="form-control" value="{{ $data->name ?? '' }}" placeholder="Andika Pratama">
		</div>

		<div class="mb-2">
			<label for="identity_type" class="form-label">Identitas</label>
			<select class="form-control" name="identity_type" id="identity_type">
				<option value="">-- Select Identitas --</option>
				<option value="KTP" {{ ($data->identity_type ?? '') == "KTP" ? "selected" : "" }}>KTP</option>
				<option value="SIM" {{ ($data->identity_type ?? '') == "SIM" ? "selected" : "" }}>SIM</option>
				<option value="PASPOR" {{ ($data->identity_type ?? '') == "PASPOR" ? "selected" : "" }}>PASPOR</option>
			</select>
		</div>

		<div class="mb-2">
			<label for="identity_number" class="form-label">Nomor Identitas</label>
			<input type="text" name="identity_number" id="identity_number" class="form-control" value="{{ $data->identity_number ?? '' }}" placeholder="Contoh: 12345678901234567">
		</div>

		<div class="mb-2">
			<label for="phone" class="form-label">No. Telp/Hp</label>
			<input type="text" name="phone" id="phone" class="form-control" value="{{ $data->phone ?? '' }}" placeholder="Contoh: 083134449303">
		</div>
	</div>
	<div class="col-md-6">
		<div class="mb-2">
			<label for="email" class="form-label">Email (optional)</label>
			<input type="number" name="email" id="email" class="form-control" value="{{ $data->email ?? '' }}" placeholder="Contoh: example@gmail.com">
		</div>

		<div class="mb-2">
			<label for="address" class="form-label">Alamat</label>
			<textarea name="address" id="address" class="form-control" rows="3" placeholder="Contoh: Jl. Bambu no. 30">{{ $data->address ?? '' }}</textarea>
		</div>
	</div>
</div>