<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title_view ?></h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header text-right">
                    <a class="btn btn-secondary btn-sm" href="<?= base_url('datadeklarasi') ?>">
                        <i class="fas fa-chevron-left"></i>&nbsp;Back
                    </a>
                </div>
                <div class="card-body">
                    <form id="form">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- First Set of Fields -->
                                <div class="form-group row">
                                    <label class="col-sm-5" for="kode_deklarasi">Kode Deklarasi</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="kode_deklarasi" name="kode_deklarasi" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5" for="tanggal">Tanggal</label>
                                    <div class="col-sm-7">
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="tanggal" id="tanggal" placeholder="DD-MM-YYYY" autocomplete="off"/>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5" for="nama_pengajuan">Nama yang mengajukan</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="nama_pengajuan" name="nama_pengajuan" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5" for="jabatan">Jabatan</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="jabatan" id="jabatan">
                                            <option value="">-- Pilih --</option>
                                            <option value="Magang">Magang</option>
                                            <option value="Karyawan">Karyawan</option>
                                            <option value="Supervisor">Supervisor</option>
                                            <option value="Manager">Manager</option>
                                            <option value="General Manager">General Manager</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Second Set of Fields -->
                                <div class="form-group row">
                                    <label class="col-sm-5" for="nama_dibayar">Nama Penerima </label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="nama_dibayar" name="nama_dibayar" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5" for="tujuan">Tujuan</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="tujuan" name="tujuan" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5" for="sebesar">Sebesar</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="sebesar" required>
                                        <input type="hidden" class="form-control" id="hidden_sebesar" name="sebesar" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-5" for="status">Status</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="status" id="status">
                                            <option value="">-- Pilih --</option>
                                            <option value="Waiting">Waiting</option>
                                            <option value="On Process">On Process</option>
                                            <option value="Done">Done</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container mt-5">
                            <div class="row text-center">
                                <!-- Header -->
                                <div class="col-md-4">
                                    <p>Yang Melakukan,</p>
                                </div>
                                <div class="col-md-4">
                                    <p>Mengetahui,</p>
                                </div>
                                <div class="col-md-4">
                                    <p>Menyetujui,</p>
                                </div>
                            </div>

                            <div class="row text-center mb-3">
                                <!-- Empty space for signature -->
                                <div class="col-md-4">
                                    <br><br><br>
                                </div>
                                <div class="col-md-4" id="knowName">
                                    <!-- Status will be inserted here -->
                                </div>
                                <div class="col-md-4" id="agreeName">
                                    <!-- Status will be inserted here -->
                                </div>
                            </div>

                            <div class="row text-center mb-3">
                                <!-- Signature line -->
                                <div class="col-md-4">
                                    <p>_____________________</p>
                                </div>
                                <div class="col-md-4" id="knowStts">
                                    <!-- Name will be inserted here -->
                                </div>
                                <div class="col-md-4" id="agreeStts">
                                    <!-- Name will be inserted here -->
                                </div>
                            </div>

                            <div class="row text-center mb-5">
                                <!-- Buttons to trigger modal -->
                                <div class="col-md-4">
                                    <!-- This column is left empty -->
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="appBtn" class="btn btn-secondary" data-toggle="modal" data-target="#appModal" data-id="<?= $id ?>">Click Me</button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="appBtn2" class="btn btn-secondary" data-toggle="modal" data-target="#appModal" data-id="<?= $id ?>">Click Me</button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs -->
                        <input type="hidden" name="id" id="id" value="<?= $id ?>">
                        <?php if (!empty($aksi)) { ?>
                            <input type="hidden" name="aksi" id="aksi" value="<?= $aksi ?>">
                        <?php } ?>
                        <?php if ($id == 0) { ?>
                            <input type="hidden" name="kode" id="kode" value="<?= $kode ?>">
                        <?php } ?>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-sm aksi"></button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="appModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approval</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="<?= base_url('deklarasi/approve') ?>">
            <div class="form-group">
                <label for="app_name">Nama</label>
                <input type="text" class="form-control" name="app_name" id="app_name" aria-describedby="emailHelp">
                <!-- HIDDEN INPUT -->
                 <input type="hidden" id="hidden_id" name="hidden_id" value="">
            </div>
            <div class="form-group">
                <label for="app_status">Approve</label>
                <select id="app_status" name="app_status" class="form-control">
                    <option selected disabled>Choose...</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="revised">Revised</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('template/footer'); ?>
<?php $this->load->view('template/script'); ?>

<script>
    
    $(document).ready(function() {
        var id = $('#id').val();
        var aksi = $('#aksi').val();
        var kode = $('#kode').val();

        if (id == 0) {
            $('.aksi').text('Save');
            $('#kode_deklarasi').val(kode).attr('readonly', true);
        } else {
            $('.aksi').text('Update');
            $("select option[value='']").hide();
            $.ajax({
                url: "<?php echo site_url('datadeklarasi/edit_data') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    moment.locale('id')
                    $('#id').val(data.id);
                    $('#kode_deklarasi').val(data.kode_deklarasi).attr('readonly', true);
                    $('#tanggal').val(moment(data.tanggal).format('DD-MM-YYYY'));
                    $('#nama_pengajuan').val(data.nama_pengajuan);
                    $('#jabatan').val(data.jabatan);
                    $('#nama_dibayar').val(data.nama_dibayar);
                    $('#tujuan').val(data.tujuan);
                    $('#sebesar').val(data.sebesar);
                    $('#status').val(data.status);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error get data from ajax');
                }
            });
        }

        if (aksi == "read") {
            $('.aksi').hide();
            $('#id').prop('readonly', true);
            $('#tanggal').prop('disabled', true);
            $('#nama_pengajuan').prop('readonly', true);
            $('#jabatan').prop('disabled', true);
            $('#nama_dibayar').prop('readonly', true);
            $('#tujuan').prop('readonly', true);
            $('#sebesar').prop('readonly', true);
            $('#status').prop('disabled', true);
        }

        $("#form").submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            if (!$form.valid()) return false;
            var url;
            if (id == 0) {
                url = "<?php echo site_url('datadeklarasi/add') ?>";
            } else {
                url = "<?php echo site_url('datadeklarasi/update') ?>";
            }

            $.ajax({
                url: url,
                type: "POST",
                data: $('#form').serialize(),
                dataType: "JSON",
                success: function(data) {
                    if (data.status) //if success close modal and reload ajax table
                    {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Your data has been saved',
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            location.href = "<?= base_url('datadeklarasi') ?>";
                        })
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error adding / update data');
                }
            });
        });

        $("#form").validate({
            rules: {
                tanggal: {
                    required: true,
                },
                nama_pengajuan: {
                    required: true,
                },
                jabatan: {
                    required: true,
                },
                nama_dibayar: {
                    required: true,
                },
                tujuan: {
                    required: true,
                },
                sebesar: {
                    required: true,
                },
                status: {
                    required: true,
                },
            },
            messages: {
                tanggal: {
                    required: "tanggal Harus Diisi",
                },
                nama_pengajuan: {
                    required: "Nama Yang Mengajukan Harus Diisi",
                },
                jabatan: {
                    required: "Jabatan Harus Diisi",
                },
                nama_dibayar: {
                    required: "Nama Yang Menerima Pembayaran Harus Diisi",
                },
                tujuan: {
                    required: "Tujuan Harus Diisi",
                },
                sebesar: {
                    required: "Sebesar Harus Diisi",
                },
                status: {
                    required: "Status Harus Diisi",
                },
            },
            errorPlacement: function(error, element) {
                if (element.parent().hasClass('input-group')) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
        })
    })

    $('#tanggal').datepicker({
        dateFormat: 'dd-mm-yy',
        minDate: new Date(),
    });

 $('#sebesar').on('input', function() {
            let value = $(this).val().replace(/[^,\d]/g, '');
            let parts = value.split(',');
            let integerPart = parts[0];

            // Format tampilan dengan pemisah ribuan
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Set nilai yang diformat ke tampilan
            $(this).val(parts[1] !== undefined ? integerPart + ',' + parts[1] : integerPart);

            // Hapus semua pemisah ribuan untuk pengiriman ke server
            let cleanValue = value.replace(/\./g, '');

            // Anda mungkin ingin menyimpan nilai bersih ini di input hidden atau langsung mengirimkannya ke server
            $('#hidden_sebesar').val(cleanValue);
        });

</script>