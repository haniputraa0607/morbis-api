<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Foundation\Mix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function listQueue(Request $request)
    {
        $result = [];
        $queues = Queue::whereDate('created_at', date('Y-m-d'))->orderBy('no', 'asc')->get()->toArray();

        foreach ($queues ?? [] as $key => $queue) {
            $result[] = [
                'id'     => $queue['id'],
                'code'   => $queue['code'],
                'status' => $queue['status'] == 'pending' ? 'Menunggu' : ($queue['status'] == 'current' ? 'Sekarang' : 'Selesai'),
            ];
        }

        return $this->ok('Berhasil mendapat daftar antrian', $result);

    }

    public function detailQueue(Request $request, $status = 'current'): JsonResponse
    {
        $queue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'current')->first();
        if($status == 'current'){
            $mess = 'Berhasil mendapat antrian saat ini';

            if(!$queue){
                $queue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'pending')->orderBy('no', 'asc')->first();
                if(!$queue){
                    return $this->error('Tidak ada antrian');
                }
                $queue->update(['status' => 'current']);
            }
        } else {
            if(!$queue){
                return $this->error('Tidak ada antrian');
            }
            $current = $queue;
            if ($status == 'prev'){
                $mess = 'Berhasil mendapat antrian sebelumnya';

                $queue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'pending')->where('no', '<', $queue['no'])->orderBy('no', 'desc')->first();
                if(!$queue){
                    return $this->error('Tidak ada antrian sebelumnya');
                }
            }elseif($status == 'next'){
                $mess = 'Berhasil mendapat antrian selanjutnya';

                $queue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'pending')->where('no', '>', $current['no'])->orderBy('no', 'asc')->first();
                if(!$queue){
                    return $this->error('Tidak ada antrian selanjutnya');
                }

            }
            $current->update(['status' => 'pending']);
            $queue->update(['status' => 'current']);
        }

        $result = [
            'id'   => $queue['id'],
            'code' => $queue['code']
        ];
        return $this->ok($mess, $result);
    }

    public function finishedQueue(Request $request): mixed
    {
        $admin = $request->user();

        $queue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'current')->first();
        if(!$queue){
            $queue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'pending')->orderBy('no', 'asc')->first();
            if(!$queue){
                return $this->error('Tidak ada antrian');
            }
        }

        $queue->update([
            'admin_id' => $admin['id'],
            'status'   => 'finished'
        ]);

        $nextQueue = Queue::whereDate('created_at', date('Y-m-d'))->where('status', 'pending')->where('no', '>', $queue['no'])->orderBy('no', 'asc')->first();
        $nextQueue->update(['status' => 'current']);


        $result = [
            'id'   => $queue['id'],
            'code' => $queue['code']
        ];

        return $this->ok('Berhasil menyelesaikan antrian', $result);

    }
}
