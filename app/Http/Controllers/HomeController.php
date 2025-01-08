<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            //get all data campaign
            $listSlider = DB::table('slider')->get();
            //$listEvent = DB::table('event')->get();

            // Paginate data event
            $listEvent = DB::table('event')->paginate(3); // Ubah 3 sesuai dengan jumlah item per halaman yang diinginkan

            // Mengirim data ke tampilan
            return view('home', [
                'data' => $listSlider,
                'event' => $listEvent
            ]);
        } catch (\Exception $e) {
            //dd($e);
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('home', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function dashboard()
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('error', 'You must be logged in to access the dashboard.');
        } else {
            return view('dashboard');
        }
    }
    
}
