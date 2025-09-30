@extends('sb-admin-2.layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .card-header {
        background-color: #fff;
    }
    .mr-0 {
        margin-right: 0;
    }
    .ml-auto {
        margin-left: auto;
    }
    .d-block {
        display: block;
    }
    .button-group a {
        margin-bottom: 10px;
    }
</style>

<!-- CSS custom -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

<div class="card shadow mb-4 custom-card-header">
    <div class="card-header py-3">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Report Pendaftaran Peserta Event</h1>
        <p class="mb-4" style="color:black">
            loremipsum loremipsum loremipsum loremipsum loremipsum loremipsum
        </p>
        @if(isset($error))
        <div align="center">
            <text style="color:red">{{ $error }}</text>
        </div>
        @endif
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="GET" action="/getreportevent">
                    @csrf
                    <div class="mb-3">
                        <label for="outlet" class="form-label">Event</label>
                        <select class="form-control" id="event" name="event" required>
                            <option value="" disabled {{ request('event') ? '' : 'selected' }}>Pilih Event</option>
                            @foreach($event as $item)
                            <option value="{{ $item->id_event }}|{{ $item->title_event }}" 
                                {{ request('event') == $item->id_event . '|' . $item->title_event ? 'selected' : '' }}>
                                {{ $item->title_event }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <button type="submit" class="btn btn-primary w-100" name="action" value="report">Lihat</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-success w-100" name="action" value="download">Download</button>
                        </div>
                    </div>                                       
                </form>
            </div>
        </div>

        <hr>
        <div class="table-responsive">
            <table id="datatablesSimple" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No Registrasi</th>
                        <th>Nama Peserta</th>
                        <th>Kategori</th>
                        <th>Group</th>
                        <th>Nomor HP</th>
                        <th>Nama Team</th>
                        <th>Pembayaran</th>
                        <th>Tanggal Daftar</th>
                        <th>Status User</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data) && !$data->isEmpty())
                    @foreach ($data as $index => $item)
                    <tr>
                        <td>{{ $item->rowid }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->nama_kategori }}</td>
                        <td>{{ $item->group_type }}</td>
                        <td>{{ $item->nomor_hp }}</td>
                        <td>{{ $item->nama_team }}</td>
                        <td>{{ $item->status_pembayaran }}</td>
                        <td>{{ $item->addtime }}</td>
                        <td>{{ $item->status_user }}</td>
                        <td>
                            <div class="button-group">
                                <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#detailsModal"
                                    data-rowid="{{ $item->rowid }}"
                                    data-nama="{{ $item->nama_lengkap }}"
                                    data-nomorhp="{{ $item->nomor_hp }}"
                                    data-email="{{ $item->email }}"
                                    data-namateam="{{ $item->nama_team }}"
                                    data-statuspembayaran="{{ $item->status_pembayaran }}"
                                    data-tanggaldaftar="{{ $item->addtime }}"
                                    data-kategori="{{ $item->nama_kategori }}"
                                    data-slimsuit="{{ $item->size_jersey }}"
                                    data-noplate="{{ $item->number_plate }}"
                                    data-alamat="{{ $item->alamat_domisili }}"
                                    data-img="{{ $item->foto_akta_kia }}"
                                    data-statususer="{{ $item->status_user }}"
                                    data-group="{{ $item->group_type }}"
                                    data-biayadaftar="{{ $item->biaya_daftar }}"
                                    data-totalbayar="{{ $item->total_bayar }}"
                                    data-kodeunik="{{ $item->kode_unik }}"
                                    data-idtransaksi="{{ $item->id_transaksi }}"
                                    data-tanggallahir="{{ $item->tanggal_lahir }}"
                                    data-buktitransfer="{{ $item->foto_bukti_trf }}">
                                    Detail
                                </button>
                                <form method="POST" action="/postapproveuser">
                                    @csrf
                                    <input type="hidden" name="rowid" value="{{ $item->rowid }}">
                                    <button type="submit" name="proses" value="delete" class="btn btn-danger mb-2">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else

                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Detail Peserta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST" action="/postapproveuser" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="rowid" id="editRowid">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modalStatusPembayaran"><strong>Status Pembayaran:</strong></label>
                            <input type="text" class="form-control" id="modalStatusPembayaran" name="status_pembayaran" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalTanggalDaftar"><strong>Tanggal Daftar:</strong></label>
                            <input type="text" class="form-control" id="modalTanggalDaftar" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalNomorHp"><strong>Nomor HP:</strong></label>
                            <input type="text" class="form-control" id="modalNomorHp" name="nomor_hp" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalEmail"><strong>Email:</strong></label>
                            <input type="text" class="form-control" id="modalEmail" name="email" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalNama"><strong>Nama Peserta:</strong></label>
                            <input type="text" class="form-control" id="modalNama" name="nama" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalAlamat"><strong>Alamat:</strong></label>
                            <textarea class="form-control" id="modalAlamat" rows="3" readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label for="modalNamaTeam"><strong>Nama Team:</strong></label>
                            <input type="text" class="form-control" id="modalNamaTeam" name="nama_team" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalNoPlate"><strong>Nomor Plate:</strong></label>
                            <input type="text" class="form-control" id="modalNoPlate" name="number_plate" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalSizeSlimsuit"><strong>Size Jersey:</strong></label>
                            <input type="text" class="form-control" id="modalSizeSlimsuit" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalTanggalLahir"><strong>Tanggal Lahir:</strong></label>
                            <input type="text" class="form-control" id="modalTanggalLahir" name="tanggal_lahir" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalkategori"><strong>Kategori:</strong></label>
                            <input type="text" class="form-control" id="modalkategori" name="kategori" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalGroup"><strong>Group:</strong></label>
                            <input type="text" class="form-control" id="modalGroup" name="group" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalTotal"><strong>Total Bayar:</strong></label>
                            <input type="text" class="form-control" id="modalTotal" name="total_bayar" readonly>
                        </div>
                        <div class="form-group">
                            <label><strong>Foto Akta/Kia:</strong></label>
                            <a id="imageLink" href="#" target="_blank">
                                <img id="currentImage" src="" alt="Current Image" style="max-width: 100px; max-height: 100px;">
                            </a>
                        </div>
                        <div class="form-group">
                            <label><strong>Bukti Transfer:</strong></label>
                            <a id="imageLinkTransfer" href="#" target="_blank">
                                <img 
                                    id="currentImageTransfer" 
                                    src="" 
                                    alt="Current Image" 
                                    style="max-width: 100px; max-height: 100px;">
                            </a>
                        </div>                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="proses-edit" name="proses" value="approve" class="btn btn-primary">Approve</button>
                        <a href="#" id="proses-edit" class="btn btn-success" target="_blank">Confirmation</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
@endif

<!-- Page level plugins -->
<script src="{{ asset('vendor/jquery/jquery-3.3.1.min.js')}}"></script>
<script src="{{ asset('vendor/jquery/jquery.validate.min.js')}}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#datatablesSimple').DataTable({
            "lengthMenu": [10, 20, 50, 100],
            "pageLength": 10,
            responsive: true,
            searching: true
        });

        $('#detailsModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var rowid = button.data('rowid');
            var nama = button.data('nama');
            var nomorHp = String(button.data('nomorhp'));
            var email = button.data('email');
            var namaTeam = button.data('namateam');
            var statusPembayaran = button.data('statuspembayaran');
            var tanggalDaftar = button.data('tanggaldaftar');
            var kategori = button.data('kategori');
            var tanggalLahir = button.data('tanggallahir');
            var group = button.data('group');
            var slimsuit = button.data('slimsuit');
            var noplate = button.data('noplate');
            var alamat = button.data('alamat');
            var img = button.data('img');
            var statusUser = button.data('statususer');
            var buktiTransfer = button.data('buktitransfer');
            var biayaDaftar = button.data('biayadaftar');
            var kodeUnik = button.data('kodeunik');
            var totalBayar = button.data('totalbayar');
            var idTransaksi = button.data('idtransaksi');

            var modal = $(this);
            modal.find('#editRowid').val(rowid);
            modal.find('#modalNama').val(nama);
            modal.find('#modalNomorHp').val(nomorHp);
            modal.find('#modalEmail').val(email);
            modal.find('#modalNamaTeam').val(namaTeam);
            modal.find('#modalStatusPembayaran').val(statusPembayaran);
            modal.find('#modalTanggalDaftar').val(tanggalDaftar);
            modal.find('#modalTanggalLahir').val(tanggalLahir);
            modal.find('#modalkategori').val(kategori);
            modal.find('#modalGroup').val(group);
            modal.find('#modalSizeSlimsuit').val(slimsuit);
            modal.find('#modalNoPlate').val(noplate);
            modal.find('#modalAlamat').val(alamat);
            modal.find('#modalTotal').val(totalBayar);
            modal.find('#currentImage').attr('src', "{{ url('/pushbikeklaten/public/KIA_KK') }}/" + img);
            modal.find('#imageLink').attr('href', "{{ url('/pushbikeklaten/public/KIA_KK') }}/" + img);
            modal.find('#currentImageTransfer').attr('src', "{{ url('/pushbikeklaten/public/invoice') }}/" + buktiTransfer);
            modal.find('#imageLinkTransfer').attr('href', "{{ url('/pushbikeklaten/public/invoice') }}/" + buktiTransfer);

            if (!buktiTransfer || !buktiTransfer.includes('.jpg')) {
                const textElement = $('<span>')
                    .text('Belum bayar')
                    .css({
                        color: 'red',
                        fontWeight: 'bold'
                    });

                const parentLink = modal.find('#imageLinkTransfer');
                parentLink.empty().append(textElement);
                parentLink.attr('href', '#');
            }


            if (statusUser === 'PENDING') {
                modal.find('button[name="proses"][value="approve"]').show();
                modal.find('a#proses-edit').show()
            } else if (statusUser === 'SENTBACK') {
                modal.find('button[name="proses"][value="approve"]').show();
                modal.find('a#proses-edit').show();
            }else if (statusUser === 'CONFIRMATION') {
                modal.find('button[name="proses"][value="approve"]').show();
                modal.find('a#proses-edit').hide();
            }
            else {
                modal.find('button[name="proses"][value="approve"]').hide();
                modal.find('a#proses-edit').hide();
            }

            //kirim WA
            if (nomorHp.startsWith("0")) {
                nomorHp = "62" + nomorHp.substring(1);
            }
            var waLink = "https://wa.me/" + nomorHp  + "?text=Hallo%20Racer%2C%2C%0aMohon%20konfrmasi%20untuk%20pendftaran%20Klaten%20Pushbike%20Competition%202025%0aNama%20Rider%20%3A%20"+nama+"%0aKatergori%20%3A%20"+kategori+"%0aApakah%20mau%20untuk%20melanjutkan%20registrasi%20kak%3F%0aAdmin%20tunggu%20konfirmasinya%20hari%20ini%20jam%2015%3A00%20wib%20dengan%20melampirkan%20bukti%20Transfer.%0aTerimakasih%2C%2C%0a%0aInformasi%20Pembayaran%3A%0aBiaya%20Registrasi%20%3A%20Rp%20"+biayaDaftar+"%0aKode%20Unik%20%3A%20"+kodeUnik+"%0aTotal%20%3A%20Rp%20"+totalBayar+"%0a%0ASetelah%20Anda%20melakukan%20pembayaran%2C%20silahkan%20konfirmasi%20pembayaran%20disini%0ahttps%3A%2F%2Fpushbikeklaten.com%2Fstatustransaksi%2F"+idTransaksi;
            // var waLink = "https://wa.me/" + nomorHp  + "?text=Hallo%20Robo%20Racer,,%0aMohon%20konfrmasi%20untuk%20pendftaran%20Robo%20Race%202025%0aNama%20Rider%20:%20"+nama+"%0aKatergori%20:%20"+kategori+"%0aApakah%20mau%20untuk%20melanjutkan%20registrasi%20kak?%0aRobo%20tunggu%20konfirmasinya%20hari%20ini%20jam%2015:00%20wib%20dengan%20melampirkan%20bukti%20Transfer.%0aTerimakasih,,%0a%0aBCA%20030-134-4952%20Wisnu%20Bhakti%20Prasetyo";
            $(this).find('a#proses-edit').attr('href', waLink).off('click').on('click', function(e) {
                e.preventDefault(); 
                $.ajax({
                    url: '/postapproveuser',
                    type: 'POST',
                    data: {
                        rowid: rowid,
                        proses: 'CONFIRMATION'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                        window.open(waLink, '_blank');
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log('Response Text: ' + xhr.responseText);
                        alert('Gagal update data.');
                    }
                });
            });

        });
    });
</script>

@endsection
