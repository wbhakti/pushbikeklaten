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
                return redirect('/');
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
                return redirect('/');
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
                return redirect('/');
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
                return redirect('/');
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
                return redirect('/');
            }
            
            $listKategori = DB::table('kategori')
            ->where('is_delete', 'false')
            ->get();

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
                return redirect('/');
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

}