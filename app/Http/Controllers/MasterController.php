<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function slider()
    {
        try{
            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }
            //get all data campaign
            $listSlider = DB::table('slider')->get();

            // Mengirim data ke tampilan
            return view('masterslider', [
                'data' => $listSlider
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('masterslider', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function postslider(Request $request)
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == "save"){
                // Proses upload file
                if ($request->hasFile('img_slider')) {
                    $file = $request->file('img_slider');
                    $fileName = date('YmdHis') . '_' . $file->getClientOriginalName();
                    $file->move('public/img', $fileName);
                } else {
                    return redirect()->back()->with('success', 'Image upload failed.');
                }

                // Simpan data ke database
                DB::table('slider')->insert([
                    'img_slider' => $fileName,
                    'title_slider' => $request->input('title_slider'),
                    'desc_slider' => $request->input('desc_slider'),
                    'action' => $request->input('action_slider'),
                    'is_active' => 'Y',
                ]);

                return redirect()->back()->with('success', 'Save success!');
            }
            else if($request->input('proses') == "edit"){
                // Proses upload file jika ada file baru
                if ($request->hasFile('img_slider')) {
                    $file = $request->file('img_slider');
                    $fileName = date('YmdHis') . '_' . $file->getClientOriginalName();
                    $file->move('public/img', $fileName);
                } else {
                    $fileName = $request->input('old_img_slider');
                }

                // Update data di database
                DB::table('slider')
                    ->where('rowid', $request->input('rowid'))
                    ->update([
                        'img_slider' => $fileName,
                        'title_slider' => $request->input('title_slider'),
                        'desc_slider' => $request->input('desc_slider'),
                        'action' => $request->input('action_slider'),
                        'is_active' => $request->input('is_active'),
                    ]);

                return redirect()->back()->with('success', 'Edit slider success!');
            }
            else{
                //delete
                DB::table('slider')->where('rowid', $request->input('rowid'))->delete();

                return redirect()->back()->with('success', 'Delete slider success!');
            }
        }catch(\Exception $e){
            Log::error('Error occurred : ' . $e->getMessage());
            return redirect()->back()->with('success', 'Save slider failed : ' . $e->getMessage());
        }
    }

    public function event()
    {
        try{
            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }

            $listEvent = DB::table('event')->get();
            $listForm = DB::table('form')->get();

            // Mengirim data ke tampilan
            return view('masterevent', [
                'event' => $listEvent,
                'listform' => $listForm
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('mastercontent', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function postevent(Request $request)
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == "save"){
                // Proses upload file
                if ($request->hasFile('img_event')) {
                    $file = $request->file('img_event');
                    $fileName = date('YmdHis') . '_' . $file->getClientOriginalName();
                    $file->move('public/img', $fileName);
                } else {
                    return redirect()->back()->with('success', 'Image upload failed.');
                }

                // Simpan data ke database
                DB::table('event')->insert([
                    'id_event' => 'event'.date('YmdHis'),
                    'img_event' => $fileName,
                    'title_event' => $request->input('title_event'),
                    'desc_event' => $request->input('desc_event'),
                    'action' => $request->input('action_event'),
                    'is_active' => 'Y',
                    'input_form' => $request->input('category_event'),
                    'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
                ]);

                return redirect()->back()->with('success', 'Save success!');
            }
            else if($request->input('proses') == "edit"){
                // Proses upload file jika ada file baru
                if ($request->hasFile('img_event')) {
                    $file = $request->file('img_event');
                    $fileName = date('YmdHis') . '_' . $file->getClientOriginalName();
                    $file->move('public/img', $fileName);
                } else {
                    $fileName = $request->input('old_img_event');
                }

                // Update data di database
                DB::table('event')
                    ->where('rowid', $request->input('rowid'))
                    ->update([
                        'img_event' => $fileName,
                        'title_event' => $request->input('title_event'),
                        'desc_event' => $request->input('desc_event'),
                        'action' => $request->input('action_event'),
                        'is_active' => $request->input('is_active'),
                    ]);

                return redirect()->back()->with('success', 'Edit event success!');
            }
            else{
                //delete
                DB::table('event')->where('rowid', $request->input('rowid'))->delete();

                return redirect()->back()->with('success', 'Delete event success!');
            }
        }catch(\Exception $e){
            Log::error('Error occurred : ' . $e->getMessage());
            return redirect()->back()->with('success', 'Save event failed : ' . $e->getMessage());
        }
    }

    public function kategori()
    {
        try{
            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }
            
            $listKategori = DB::table('kategori')->get();

            // Mengirim data ke tampilan
            return view('masterkategori', [
                'data' => $listKategori
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('masterkategori', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function postkategori(Request $request)
    {
        try{

            if (!session()->has('user_id')) {
                return redirect('/login')->with('error', 'You must be logged in to access the menu.');
            }
            
            if($request->input('proses') == "save"){
                
                // Simpan data ke database
                DB::table('kategori')->insert([
                    'id_kategori' => 'kategori_'.str_replace(' ', '', $request->input('nama_kategori')),
                    'nama_kategori' => $request->input('nama_kategori'),
                ]);

                return redirect()->back()->with('success', 'Save success!');
            }
            else{
                //delete
                DB::table('kategori')->where('rowid', $request->input('rowid'))->delete();

                return redirect()->back()->with('success', 'Delete kategori success!');
            }
        }catch(\Exception $e){
            Log::error('Error occurred : ' . $e->getMessage());
            return redirect()->back()->with('success', 'error : ' . $e->getMessage());
        }
    }

    public function report()
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('error', 'You must be logged in to access the menu.');
        }
        
        return view('report');
    }

    public function postreport(Request $request)
    {
        try {

            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $listData = DB::table('peserta')
            ->join('kategori', 'peserta.kategori_id', '=', 'kategori.id_kategori')
            ->where('peserta.addtime', '>=', $startDate . ' 00:00:00')
            ->where('peserta.addtime', '<=', $endDate . ' 23:59:59')
            ->where('peserta.is_delete', '0')
            ->orderBy('peserta.addtime', 'asc')
            ->select(
                'peserta.*',
                'kategori.nama_kategori as kategori'
            )
            ->get();

            if ($listData->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk tanggal yang dipilih.');
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

            $sheet->setCellValue('A3', 'Periode: ' . date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate)));
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

            $fileName = 'LaporanPendaftaran_' . $startDate . '_sampai_' . $endDate . '.xlsx';
            $filePath = public_path($fileName);
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('postreport Error occurred report: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan ambil data.');
        }
    }

}