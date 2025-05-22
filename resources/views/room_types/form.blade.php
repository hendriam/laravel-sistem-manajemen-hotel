<div class="row">
	<div class="col-md-6">
		@if(session('success'))
			<div class="alert alert-success">
				{{ session('success') }}
			</div>
		@endif

		<div class="mb-2">
			<label for="name" class="form-label">Nama Lantai</label>
			<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $data->name ?? '') }}" placeholder="Contoh: Single, Double, Suite">
			@error('name')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
			@enderror
		</div>

		<div class="mb-2">
			<label for="description" class="form-label">Keterangan</label>
			<textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Masukkan keterangan (optional)">{{ old('description', $data->description ?? '') }}</textarea>
			@error('description')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
			@enderror
		</div>
	</div>
</div>