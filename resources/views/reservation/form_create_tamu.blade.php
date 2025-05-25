<!-- Modal -->
		<form action="{{ route('guest.store') }}" method="post" id="formCreateGuest" enctype="multipart/form-data" autocomplete="off">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalCreateGuestLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="mb-2">
								<label for="name" class="form-label">Nama Tamu</label>
								<input type="text" name="name" id="name" class="form-control" placeholder="Andika Pratama">
							</div>

							<div class="mb-2" id="identityType">
								<label for="identity_type" class="form-label">Identitas</label>
								<select class="form-control" name="identity_type" id="identity_type">
									<option value="">-- Select Identitas --</option>
									<option value="KTP">KTP</option>
									<option value="SIM">SIM</option>
									<option value="PASPOR">PASPOR</option>
								</select>
							</div>

							<div class="mb-2">
								<label for="identity_number" class="form-label">Nomor Identitas</label>
								<input type="text" name="identity_number" id="identity_number" class="form-control" placeholder="Contoh: 12345678901234567">
							</div>

							<div class="mb-2">
								<label for="phone" class="form-label">No. Telp/Hp</label>
								<input type="text" name="phone" id="phone" class="form-control" placeholder="Contoh: 083134449303">
							</div>
						</div>

						<div class="col-md-6">
							<div class="mb-2">
								<label for="email" class="form-label">Email (optianal)</label>
								<input type="number" name="email" id="email" class="form-control" placeholder="Contoh: example@gmail.com">
							</div>
		
							<div class="mb-2">
								<label for="address" class="form-label">Alamat</label>
								<textarea name="address" id="address" class="form-control" rows="3" placeholder="Contoh: Jl. Bambu no 30"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-window-close"></i> Batal</button>
					<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <i class='fas fa-spinner fa-spin' style="display: none"></i> Simpan</button>

					<!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
					<!-- <button type="submit" class="btn btn-primary"><i class='fas fa-spinner fa-spin' style="display: none" id="load2x"></i> Save changes</button> -->
				</div>
			</div>
		</form>
