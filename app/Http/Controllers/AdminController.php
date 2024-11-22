<?php

namespace App\Http\Controllers;

use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\GenerateQrcode;
use App\Models\VoteRecord;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function adminPage(Request $request) 
    {
        try
        {
            $voteEvents = VoteEvent::where('is_delete', 0)->orderBy('created_at', 'desc')->get()->map(function ($event) {
                $now = Carbon::now();
                $start = Carbon::parse($event->start_time);
                $end = Carbon::parse($event->end_time);

                if ($now->lessThan($start)) {
                    $event->status = 0; // 未開始
                } elseif ($now->between($start, $end)) {
                    $event->status = 1; // 進行中
                } else {
                    $event->status = 2; // 已結束
                }
                return $event;
            });

            $response = [];
            $response['vote_event'] = $voteEvents;
            $response['total'] = count($voteEvents);

            return view('admin.index', $response);
        }
        catch (\Exception $e) 
        {
            echo $e;
        }
    }

    // ajax每20秒取資料
    public function postCheckVoteSituation($event_id)
    {
        $voted_qrcodes = GenerateQrcode::getVotedQrcodes($event_id);

        $result = [];
        $result = [
            'qrcodes' => $voted_qrcodes,
            'system_time' => date('Y-m-d H:i:s', time())
        ];

        return response()->json($result);
    }

    public function adminList()
    {
        $adminUser = Admin::all();

        $response = [];
        $response = [
            'users' => $adminUser,
        ];
        return view('admin.admin_list', $response);
    }

    public function createAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        try {
            $adminUser = new Admin();
            $adminUser->name = $validatedData['name'];
            $adminUser->email = $validatedData['email'];
            $adminUser->password = Hash::make($validatedData['password']);
            $adminUser->save();

            return response()->json(['message' => '新增成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '新增失敗，請重試', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $user = Admin::where('email', $validatedData['email'])->first();

        if ($user) {
            $user->delete();
            return response()->json(['message' => '使用者已刪除'], 200);
        } else {
            return response()->json(['message' => '找不到使用者'], 404);
        }
    }

    public function updateAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        $user = Admin::findOrFail($validatedData['user_id']);

        // 更新用戶資料
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // // 如果有提供新密碼，則更新
        // if (!empty($validatedData['password'])) {
        //     $user->password = Hash::make($validatedData['password']);
        // }

        $user->save();

        return response()->json(['message' => '用戶更新成功'], 200);
    }
}
