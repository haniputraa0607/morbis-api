<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function createQueue(Request $request): JsonResponse
    {

        $latestCode = Queue::whereDate('created_at', date('Y-m-d'))->latest('no')->first();
        $no = !$latestCode ? 1 : $latestCode['no']+1;

        $code = 'QUE-'.date('ymd').'-'.$no;
        $code = $this->generateCode($code, $no);

        $queue = [
            'no'   => $no,
            'code' => $code
        ];

        $create = Queue::create($queue);
        if (!$create){
            return $this->error('Gagal membuat antrian');
        }

        return $this->ok('Berhasil membuat antrian', $create);

    }

    public function generateCode($code, $no){

        $check_code = Queue::where('code', $code)->first();
        if($check_code){
            $no = $no++;
            $code = 'QUE-'.date('ymd').'-'.$no;
            return $this->generateCode($code, $no);
        }

        return $code;
    }
}
