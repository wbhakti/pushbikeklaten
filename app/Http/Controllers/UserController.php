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

        $listKategori = DB::table('kategori')->get();

        return view('register', [
            'dataEvent' => $dataEvent,
            'listKategori' => $listKategori,
            'inputForm' => $inputFields,
            'idEvent' => $request->query('event')
        ]);
    }

    public function postregister(Request $request)
    {
        try{

            // Validasi input
            $tmpKategori = explode('|', $request->input('kategori'));
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
                'size_slim_suit' => $request->input('size_slim_suit'),
                'nomor_hp' => $request->input('nomor_hp'),
                'status_pembayaran' => 'BELUM_LUNAS',
                'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
                'id_event' => $request->input('idEvent'),
                'tanggal_lahir' => $request->input('tanggal_lahir'),
                'group_type' => $group,
            ]);

            // Redirect ke halaman checkout
            return view('checkout', [
                'email' => $request->input('email'),
                'nama_lengkap' => $request->input('nama_lengkap'),
                'number_plate' => $request->input('number_plate'),
                'nama_team' => $request->input('nama_team'),
                'alamat_domisili' => $request->input('alamat_domisili'),
                'tanggal_lahir' => $request->input('tanggal_lahir'),
                'kategori_id' => $namaKategori,
                'foto_akta_kia' => $filename,
                'size_slim_suit' => $request->input('size_slim_suit'),
                'nomor_hp' => $request->input('nomor_hp'),
            ]);

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan : ' . $e->getMessage());
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
                $sheet->setCellValue("E$startRow", 'Nama Team');
                $sheet->setCellValue("F$startRow", 'Kategori');
                $sheet->setCellValue("G$startRow", 'Size Jersey');
                $sheet->setCellValue("H$startRow", 'Nomor HP');
                $sheet->setCellValue("I$startRow", 'Alamat');
                $sheet->setCellValue("J$startRow", 'Pembayaran');
                $sheet->setCellValue("K$startRow", 'Status');

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
                    $sheet->setCellValue("E$row", $data->nama_team);
                    $sheet->setCellValue("F$row", $data->kategori);
                    $sheet->setCellValue("G$row", $data->size_slim_suit);
                    $sheet->setCellValue("H$row", $data->nomor_hp);
                    $sheet->setCellValue("I$row", $data->alamat_domisili);
                    $sheet->setCellValue("J$row", $data->status_pembayaran);
                    $sheet->setCellValue("K$row", $data->status_user);
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
            }

            return view('success');

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan : ' . $e->getMessage());
        }
    }

}
