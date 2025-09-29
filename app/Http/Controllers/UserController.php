<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UserController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function postlogin(Request $request)
    {
        try {

            $passHash = base64_encode(hash_hmac('sha256', $request->input('username') . ':' . $request->input('password'), '#@R4dJaAN91n?#@', true));
            // Cek apakah username dan password cocok
            $user = DB::table('user')
                ->where('username', $request->input('username'))
                ->where('password', $passHash)
                ->where('status', 'active')
                ->first();

            if ($user) {
                // Redirect ke halaman dashboard atau halaman lainnya
                session(['user_id' => $request->input('username'), 'role' => $user->role]);
                return redirect()->route('dashboard');
            } else {
                // Jika username tidak ditemukan atau password salah, kembali ke halaman login dengan pesan error
                return back()->with('error', 'Username atau password salah');
            }
        } catch (\Exception $e) {
            //dd($e);
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan : ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        // Menghapus semua data dari sesi
        $request->session()->flush();
        return redirect()->route('login');
    }

    public function register(Request $request)
    {
        $dataEvent = DB::table('event')
            ->where('id_event', $request->query('event'))
            ->first();

        if (!$dataEvent) {
            // Jika $dataEvent kosong (null)
            abort(404);
        }

        $dataForm = explode(",", $dataEvent->input_form);
        $inputFields = [];
        foreach ($dataForm as $item) {
            list($name, $label) = explode("|", $item);
            $inputFields[] = [
                'name' => trim($name),
                'label' => trim($label)
            ];
        }

        $listKategori = DB::table('kategori')
        ->where('is_delete', 'false')
        ->get();

        $dataSize = DB::table('jersey')
            ->where('id_event', $request->query('event'))
            ->get();

        return view('register', [
            'dataEvent' => $dataEvent,
            'listKategori' => $listKategori,
            'inputForm' => $inputFields,
            'dataSize' => $dataSize,
            'idEvent' => $request->query('event')
        ]);
    }

    public function postregister(Request $request)
    {
        try{

            // Validasi input
            $tmpKategori = explode('|', $request->input('kategori'));
            $tmpJersey = explode('|', $request->input('size_jersey'));
            $sizeJersey = $tmpJersey[1];
            $idKategori = $tmpKategori[0];
            $namaKategori = $tmpKategori[1];

            // Upload foto
            $filename = null;
            
            if ($request->hasFile('foto_akta_kia')) {
                if ($request->file('foto_akta_kia')->extension() == "pdf") {
                    $file = $request->file('foto_akta_kia');
                    $filename = 'foto_'.$idKategori.'_'.$request->input('nomor_hp').'.pdf';
                    $file->move(public_path('img'), $filename);

                } else {
                    $file = $request->file('foto_akta_kia');
                    $filename = 'foto_'.$idKategori.'_'.$request->input('nomor_hp').'.jpg';
                    $file->move(public_path('img'), $filename);
                }
            }

            $suffix = ''; // Suffix untuk menandakan duplikasi
            $count = 1;
            // Loop untuk memastikan number_plate unik
            while (DB::table('peserta')->where('kategori_id', $idKategori)->where('number_plate', $request->input('number_plate') . $suffix)->exists()) {
                $suffix = chr(65 + $count - 1); //'A'
                $count++;
            }

            $mtgl_lahir = new Carbon( $request->input('tanggal_lahir') );
            $mtahun_lahir = $mtgl_lahir->format('Y');
            $mbulan_lahir = $mtgl_lahir->format('m');
            $group = "MERGE";

            if ($mtahun_lahir == "2022" || $mtahun_lahir == "2021" || $mtahun_lahir == "2020"){
                if ($mbulan_lahir < 7){
                    $group = "BIG";
                } else {
                    $group = "JUNIOR";
                }
            }

            $idTransaksi = Carbon::now()->addHours(7)->format('dmYHis') . substr($request->input('nomor_hp'), -4);
            $kodeUnik = mt_rand(10, 499);
            $totalBayar = $request->input('biaya_daftar') + $kodeUnik;
        
            // Simpan data registrasi
            DB::table('peserta')->insert([
                'email' => $request->input('email'),
                'nama_lengkap' => $request->input('nama_lengkap'),
                'number_plate' => $request->input('number_plate') . $suffix,
                'nama_team' => $request->input('nama_team'),
                'alamat_domisili' => $request->input('alamat_domisili'),
                'kategori_id' => $idKategori,
                'nama_kategori' => $namaKategori,
                'foto_akta_kia' => $filename,
                'size_jersey' => $sizeJersey,
                'nomor_hp' => $request->input('nomor_hp'),
                'status_pembayaran' => 'BELUM_LUNAS',
                'status_user' => 'PENDING',
                'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
                'id_event' => $request->input('idEvent'),
                'tanggal_lahir' => $request->input('tanggal_lahir'),
                'group_type' => $group,
                'id_transaksi' => $idTransaksi,
                'kode_unik' => $kodeUnik,
                'total_bayar' => $totalBayar,
            ]);

            //kirim email
            $dataEvent = DB::table('event')->where('id_event', '=', $request->input('idEvent'))->first();
            $data = [
                'idTransaksi' => $idTransaksi,
                'email' => $request->input('email'),
                'nama_lengkap' => $request->input('nama_lengkap'),
                'number_plate' => $request->input('number_plate'),
                'nama_team' => $request->input('nama_team'),
                'kategori_id' => $namaKategori,
                'size_jersey' => $sizeJersey,
                'nomor_hp' => $request->input('nomor_hp'),
                'totalbayar' => $totalBayar,
                'bank' => $dataEvent->nama_bank,
                'namarek' => $dataEvent->nama_rekening,
                'norek' => $dataEvent->nomer_rekening,
            ];

            $url_base = url('/');
            
            // Konten HTML untuk email
            $bodyEmail = '
            <html>
            <body>
                <h1 style="color: #3490dc;">[INVOICE]</h1>
                <h1 style="color: #3490dc;">Terima kasih atas pendaftaran Klaten Pushbike Competition 2025</h1>
                <p>Selamat bergabung, kami tunggu pembayaran kamu yaa..,</p>
                <p>Berikut adalah informasi pendaftaran Anda:</p>

                <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 50%;">
                    <tr>
                        <th style="background-color: #f2f2f2; text-align: left;">Informasi</th>
                        <th style="background-color: #f2f2f2; text-align: left;">Detail</th>
                    </tr>
                    <tr>
                        <td>ID Transaksi</td>
                        <td>' . $data['idTransaksi'] . '</td>
                    </tr>
                    <tr>
                        <td>Nama Peserta</td>
                        <td>' . $data['nama_lengkap'] . '</td>
                    </tr>
                    <tr>
                        <td>Kategori</td>
                        <td>' . $data['kategori_id'] . '</td>
                    </tr>
                    <tr>
                        <td>Number Plate</td>
                        <td>' . $data['number_plate'] . '</td>
                    </tr>
                    <tr>
                        <td>Size Jersey</td>
                        <td>' . $data['size_jersey'] . '</td>
                    </tr>
                    <tr>
                        <td>Tim atau Komunitas</td>
                        <td>' . $data['nama_team'] . '</td>
                    </tr>
                    <tr>
                        <td>Nomor Handphone</td>
                        <td>' . $data['nomor_hp'] . '</td>
                    </tr>
                </table>

                <br>
                <h4 style="color: #28a745;">Informasi Pembayaran:</h4>
                <div>
                    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 50%;">
                        <tr>
                            <td>Biaya Registrasi </td>
                            <td>Rp ' . number_format(($request->input('biaya_daftar')), 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td>Kode Unik</td>
                            <td>Rp ' . number_format($kodeUnik, 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td><strong>Total Bayar</strong></td>
                            <td><strong>Rp ' . number_format($data['totalbayar'], 0, ',', '.') . '</strong></td>
                        </tr>
                    </table>
                    <h5 style="color: #007bff;">Informasi Rekening:</h5>
                    <ul style="list-style-type: none; padding: 0;">
                        <li><strong>Bank:</strong> '.$data['bank'].'</li>
                        <li><strong>Nomor Rekening:</strong> '.$data['norek'].'</li>
                        <li><strong>Atas Nama:</strong> '.$data['namarek'].'</li>
                    </ul>
                </div>
                
                <p>Setelah Anda melakukan pembayaran, silahkan <a href="'.$url_base.'/statustransaksi/'.$data['idTransaksi'].'"> konfirmasi pembayaran disini</a></p>
                <br>
                <p>Silakan hubungi kami jika Anda memiliki pertanyaan lebih lanjut.</p>
                <p>Salam,</p>
                <p><strong>Customer Support</strong></p>
            </body>
            </html>
            ';

            Mail::html($bodyEmail, function ($message) use ($data) {
                $message->to($data['email'], $data['nama_lengkap']);
                $message->subject('Informasi Pembayaran');
            });

            return redirect()->route('statusTransaksi', ['id_transaksi' => $idTransaksi]);

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan : ' . $e->getMessage());
        }
    }


    public function cekstatus()
    {
        try{

            return view('cekstatus');

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            abort(404);
        }
        
    }

    public function statusTransaksi($id_transaksi)
    {
        try{

            $dataPeserta = DB::table('peserta')
            ->join('event', 'peserta.id_event', '=', 'event.id_event')
            ->where('id_transaksi', $id_transaksi)
            ->select('peserta.*', 'event.*')
            ->first();

            if (!$dataPeserta) {
                abort(404);
            }

            // Redirect ke halaman checkout
            return view('checkout', [
                'email' => $dataPeserta->email,
                'nama_lengkap' => $dataPeserta->nama_lengkap,
                'number_plate' => $dataPeserta->number_plate,
                'nama_team' => $dataPeserta->nama_team,
                'alamat_domisili' => $dataPeserta->alamat_domisili,
                'tanggal_lahir' => $dataPeserta->tanggal_lahir,
                'kategori_id' => $dataPeserta->nama_kategori,
                'foto_akta_kia' => $dataPeserta->foto_akta_kia,
                'size_jersey' => $dataPeserta->size_jersey,
                'nomor_hp' => $dataPeserta->nomor_hp,

                'biaya_daftar' => $dataPeserta->biaya_daftar,
                'kode_unik' => $dataPeserta->kode_unik,
                'total_bayar' => $dataPeserta->total_bayar,
                'status_pembayaran' => $dataPeserta->status_user,
                'nama_bank' => $dataPeserta->nama_bank,
                'nama_rek' => $dataPeserta->nama_rekening,
                'nomer_rek' => $dataPeserta->nomer_rekening,
                'idTransaksi' => $dataPeserta->id_transaksi,
            ]);

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            abort(404);
        }
    }

    public function listregistration()
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/');
            }

            $listEvent = DB::table('event')->get();

            return view('listregistrationV2', [
                'event' => $listEvent
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('listregistration', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function reportevent(Request $request)
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/');
            }

            list($id_event, $title_event) = explode('|', $request->input('event'));

            $listEvent = DB::table('event')->get();

            if($request->action == "report"){

                $listPeserta = DB::table('peserta')
                ->join('event', 'peserta.id_event', '=', 'event.id_event')
                ->where('peserta.is_delete', '0')
                ->where('peserta.id_event', '=', $id_event)
                ->select('peserta.*', 'event.title_event')
                ->get();

                return view('listregistrationV2', [
                    'data' => $listPeserta,
                    'event' => $listEvent
                ]);

            }else{

                $listData = DB::table('peserta')
                ->join('kategori', 'peserta.kategori_id', '=', 'kategori.id_kategori')
                ->join('event', 'peserta.id_event', '=', 'event.id_event')
                ->where('peserta.is_delete', '0')
                ->where('peserta.id_event', '=', $id_event)
                ->orderBy('peserta.addtime', 'asc')
                ->select(
                    'peserta.*',
                    'kategori.nama_kategori as kategori',
                    'event.title_event as nama_event'
                )
                ->get();

                if ($listData->isEmpty()) {
                    return back()->with('error', 'Tidak ada data untuk event yang dipilih.');
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                //header kolom
                $sheet->setCellValue('A1', 'Laporan Pendaftaran');
                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A2', '');
                $sheet->mergeCells('A2:K2');
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A3', 'Event: ' . $title_event);
                $sheet->mergeCells('A3:K3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal Generate: ' . date('d-m-Y H:i:s', strtotime('+7 hours')));
                $sheet->mergeCells('A4:K4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $startRow = 6;

                // Header kolom
                $sheet->setCellValue("A$startRow", 'No');
                $sheet->setCellValue("B$startRow", 'ID Transaksi');
                $sheet->setCellValue("C$startRow", 'Nama Lengkap');
                $sheet->setCellValue("D$startRow", 'Number Plate');
                $sheet->setCellValue("E$startRow", 'Tanggal Lahir');
                $sheet->setCellValue("F$startRow", 'Nama Team');
                $sheet->setCellValue("G$startRow", 'Kategori');
                $sheet->setCellValue("H$startRow", 'Group');
                $sheet->setCellValue("I$startRow", 'Size Jersey');
                $sheet->setCellValue("J$startRow", 'Nomor HP');
                $sheet->setCellValue("K$startRow", 'Alamat');
                $sheet->setCellValue("L$startRow", 'Pembayaran');
                $sheet->setCellValue("M$startRow", 'Status');

                // Styling header kolom
                $sheet->getStyle("A$startRow:K$startRow")->getFont()->setBold(true);
                $sheet->getStyle("A$startRow:K$startRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A$startRow:K$startRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                //data ke sheet
                $row = $startRow + 1;
                $no = 1;
                foreach ($listData as $data) {
                    $sheet->setCellValue("A$row", $no++);
                    $sheet->setCellValue("B$row", $data->rowid);
                    $sheet->setCellValue("C$row", $data->nama_lengkap);
                    $sheet->setCellValue("D$row", $data->number_plate);
                    $sheet->setCellValue("E$row", $data->tanggal_lahir);
                    $sheet->setCellValue("F$row", $data->nama_team);
                    $sheet->setCellValue("G$row", $data->kategori);
                    $sheet->setCellValue("H$row", $data->group_type);
                    $sheet->setCellValue("I$row", $data->size_jersey);
                    $sheet->setCellValue("J$row", $data->nomor_hp);
                    $sheet->setCellValue("K$row", $data->alamat_domisili);
                    $sheet->setCellValue("L$row", $data->status_pembayaran);
                    $sheet->setCellValue("M$row", $data->status_user);
                    $row++;
                }

                // Auto-size kolom
                foreach (range('A', 'K') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                $fileName = 'LaporanPendaftaranEvent_' . $title_event . '_' . date('dmYHis', strtotime('+7 hours')) . '.xlsx';
                $filePath = public_path($fileName);
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($filePath);

                return response()->download($filePath)->deleteFileAfterSend(true);
            }
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('listregistration', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function postapproveuser(Request $request)
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/');
            }

            if($request->input('proses') == "approve"){

                //kirim Email
                $data = [
                    'rowid' => $request->input('rowid'),
                    'nama_lengkap' => $request->input('nama'),
                    'nama_kategori' => $request->input('kategori'),
                    'email' => $request->input('email'),
                    'number_plate' => $request->input('number_plate'),
                    'nama_team' => $request->input('nama_team'),
                    'nomor_hp' => $request->input('nomor_hp'),
                    'status_pembayaran' => 'LUNAS', //$request->input('status_pembayaran'),
                ];
                
                // Konten HTML untuk email
                $bodyEmail = '
                    <html>
                    <body>
                        <h1 style="color: #3490dc;">[SUKSES] Terima kasih atas pendaftaran Anda, ' . $data['nama_lengkap'] . '</h1>
                        <p>Selamat bergabung dengan kami!</p>
                        <p>Berikut adalah informasi pendaftaran Anda:</p>
                        
                        <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 50%;">
                            <tr>
                                <th style="background-color: #f2f2f2; text-align: left;">Informasi</th>
                                <th style="background-color: #f2f2f2; text-align: left;">Detail</th>
                            </tr>
                            <tr>
                                <td>ID Transaksi</td>
                                <td>' . $data['rowid'] . '</td>
                            </tr>
                            <tr>
                                <td>Nama Peserta</td>
                                <td>' . $data['nama_lengkap'] . '</td>
                            </tr>
                            <tr>
                                <td>Kategori</td>
                                <td>' . $data['nama_kategori'] . '</td>
                            </tr>
                            <tr>
                                <td>Nomor peserta</td>
                                <td>' . $data['number_plate'] . '</td>
                            </tr>
                            <tr>
                                <td>Nama Team</td>
                                <td>' . $data['nama_team'] . '</td>
                            </tr>
                            <tr>
                                <td>Nomor Handphone</td>
                                <td>' . $data['nomor_hp'] . '</td>
                            </tr>
                            <tr>
                                <td>Status Pembayaran</td>
                                <td>' . $data['status_pembayaran'] . '</td>
                            </tr>
                        </table>
                
                        <br>
                        <p>Silakan hubungi kami jika Anda memiliki pertanyaan lebih lanjut.</p>
                        <p>Salam,</p>
                        <p><strong>Customer Support</strong></p>
                    </body>
                    </html>
                ';

                Mail::html($bodyEmail, function ($message) use ($data) {
                    $message->to($data['email'], $data['nama_lengkap']);
                    $message->subject('Konfirmasi Pendaftaran');
                });
                
                DB::table('peserta')
                    ->where('rowid', $request->input('rowid'))
                    ->update([
                        'status_user' => 'APPROVED',
                        'status_pembayaran' => 'LUNAS',
                        'approved_time' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
                    ]);

                return redirect()->back()->with('success', 'Approve success!');
            }
            else if($request->input('proses') == "CONFIRMATION"){

                DB::table('peserta')
                    ->where('rowid', $request->input('rowid'))
                    ->update([
                        'status_user' => 'CONFIRMATION',
                    ]);

                // Kembalikan respons JSON
                return response()->json([
                    'message' => 'Berhasil dikonfirmasi.',
                    'status' => 'success'
                ]);
            }
            else{
                //delete
                //DB::table('peserta')->where('rowid', $request->input('rowid'))->delete();

                DB::table('peserta')
                    ->where('rowid', $request->input('rowid'))
                    ->update([
                        'is_delete' => '1',
                    ]);

                return redirect()->back()->with('success', 'Delete peserta success!');
            }
        }catch(\Exception $e){
            Log::error('Error occurred : ' . $e->getMessage());
            return redirect()->back()->with('success', 'Error : ' . $e->getMessage());
        }
    }

    public function postbuktitransfer(Request $request)
    {
        try{
            
            // Upload foto
            $filename = null;
            if ($request->hasFile('bukti_transfer')) {
                if ($request->file('bukti_transfer')->extension() == "pdf") {
                    $file = $request->file('bukti_transfer');
                    $filename = 'buktitransfer_'.date('YmdHis').'_'.$request->input('nohp').'.pdf';
                    $file->move(public_path('invoice'), $filename);
                } else {
                    $file = $request->file('bukti_transfer');
                    $filename = 'buktitransfer_'.date('YmdHis').'_'.$request->input('nohp').'.jpg';
                    $file->move(public_path('invoice'), $filename);
                }
            }

            $lastRow = DB::table('peserta')
                ->where('nomor_hp', $request->input('nohp'))
                ->where('email', $request->input('email'))
                ->orderBy('rowid', 'desc')
                ->first();

            if ($lastRow) {
                DB::table('peserta')
                    ->where('rowid', $lastRow->rowid)
                    ->update(['foto_bukti_trf' => $filename]);

                DB::table('peserta')
                    ->where('rowid', $lastRow->rowid)
                    ->update(['status_user' => 'CONFIRMATION']);
            }

            return view('success');

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan : ' . $e->getMessage());
        }
    }

}
