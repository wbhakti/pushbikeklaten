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
                $file = $request->file('foto_akta_kia');
                $filename = 'foto_'.$idKategori.'_'.$request->input('nomor_hp').'.jpg';
                $file->move(public_path('img'), $filename);
            }

            $suffix = ''; // Suffix untuk menandakan duplikasi
            $count = 1;
            // Loop untuk memastikan number_plate unik
            while (DB::table('peserta')->where('kategori_id', $idKategori)->where('number_plate', $request->input('number_plate') . $suffix)->exists()) {
                $suffix = chr(65 + $count - 1); //'A'
                $count++;
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
            ]);

            // Redirect ke halaman checkout
            return view('checkout', [
                'email' => $request->input('email'),
                'nama_lengkap' => $request->input('nama_lengkap'),
                'number_plate' => $request->input('number_plate'),
                'nama_team' => $request->input('nama_team'),
                'alamat_domisili' => $request->input('alamat_domisili'),
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
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }

            $listPeserta = DB::table('peserta')->where('is_delete', '0')->get();

            // Mengirim data ke tampilan
            return view('listregistration', [
                'data' => $listPeserta
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('listregistration', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function postapproveuser(Request $request)
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
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

            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }
            
            // Upload foto
            $filename = null;
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                $filename = 'buktitransfer_'.date('YmdHis').'_'.$request->input('nohp').'.jpg';
                $file->move(public_path('invoice'), $filename);
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
