<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\GenerateQrcode;
use App\Models\VoteRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use App\Traits\VoteHelper;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use VoteHelper;

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

    // 新增投票活動
    public function createVoteEvent(Request $request) 
    {
        try {
            $validated = $this->validateVoteEventData($request);
            
            DB::transaction(function () use ($validated) {
                // 儲存投票活動和候選人
                $voteEvent = $this->saveVoteEvent($validated);
            });

            return response()->json(['message' => '投票活動新增成功'], 200);
        } 
        catch (\Exception $e) {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return response()->json(['error' => '建立投票活動時發生錯誤。', 'details' => $e->getMessage()], 500);
        }
    }

    // 取得單一投票活動詳細內容
    public function getVoteEvent($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $this->addVoteStatus($voteEvent);

        if (!$voteEvent) {
            return response()->json(['error' => 'Vote Event not found'], 404);
        }

        $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();
        $qrcodes = $this->getQrcodeInfo($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'candidates' => $candidates,
            'qrcodes' => $qrcodes,
        ];

        return view('admin.vote', $response);
    }

    public function voteEventEditPage($event_id)
    {
        try {
            $voteEvent = VoteEvent::find($event_id);
            $this->addVoteStatus($voteEvent);

            //檢查當前狀態是否可以編輯 (only allow 尚未開始階段)
            $this->validEditPermission($voteEvent);

            $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();

            $response = [];
            $response = [
                'vote_event' => $voteEvent,
                'candidates' => $candidates,
            ];
            return view('admin.voteedit', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function editVoteEvent(Request $request, $event_id)
    {
        try {
            $validated = $this->validateVoteEventData($request);
            $this->saveVoteEvent($validated, $event_id);

            return response()->json(['message' => '投票活動更新成功'], 200);
        } 
        catch (\Exception $e) {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return response()->json(['error' => '更新投票活動時發生錯誤。', 'details' => $e->getMessage()], 500);
        }
    }

    private function validateVoteEventData(Request $request)
    {
        return $request->validate([
            'vote_name' => 'required|string|max:255',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s|after_or_equal:start',
            'candidates' => 'required|array',
            'candidates.*.number' => 'required|string|min:1|max:10',
            'candidates.*.name' => 'required|string|max:255', 
            'candidates.*.school' => 'required|string|max:255', 
            'qrcode_count' => 'required|integer|min:1',
            'manual_control' => 'required|integer',
            'max_vote' => 'integer|min:1|max:10',
            'max_winner' => 'integer|min:1|max:10'
        ]);
    }

    private function saveVoteEvent(array $validated, $event_id = null)
    {
        return DB::transaction(function () use ($validated, $event_id) {
            // 如果有 event_id，則為更新，否則為新建
            $voteEvent = $event_id ? VoteEvent::findOrFail($event_id) : new VoteEvent();
            $voteEvent->event_name = $validated['vote_name'];
            $voteEvent->max_vote_count = $validated['max_vote'];
            $voteEvent->number_of_qrcodes = $validated['qrcode_count'];
            $voteEvent->number_of_candidates = count($validated['candidates']);
            $voteEvent->number_of_winners = $validated['max_winner'];
            $voteEvent->manual_control = $validated['manual_control'];

            if ($validated['manual_control'] == 0) {
                $voteEvent->start_time = $validated['start'];
                $voteEvent->end_time = $validated['end'];
            }

            $voteEvent->save();

            // 更新候選人資料：先刪除舊的候選人及QRcode，再新增
            if ($event_id) {
                Candidate::where('event_id', $event_id)->delete();
                GenerateQrcode::where('event_id', $event_id)->delete();
            }
            foreach ($validated['candidates'] as $cand) {
                $candidate = new Candidate();
                $candidate->event_id = $voteEvent->event_id;
                $candidate->number = $cand['number'];
                $candidate->name = $cand['name'];
                $candidate->school = $cand['school'];
                $candidate->save();
            }
            // 生成 QR Codes
            for ($i = 0; $i < $validated['qrcode_count']; $i++) {
                $generateQrcode = new GenerateQrcode();
                $generateQrcode->event_id = $voteEvent->event_id;
                $generateQrcode->qrcode_string = md5($voteEvent->event_id . '_' . ($i + 1) . '_' . date("Ymd"));
                $generateQrcode->save();
            }
            return $voteEvent;
        });
    }

    public function generatePDF($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();
        $qrcodes = $this->getQrcodeInfo($event_id);

        // 初始化 DOMPDF
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // 使用默認字型作為備用字型
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsFontSubsettingEnabled(true);

        $dompdf = new Dompdf($options);

        // 載入 HTML 模板，並傳遞數據
        $html = view('pdf.voteticket', compact('voteEvent', 'candidates', 'qrcodes'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 輸出 PDF
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $voteEvent->event_name . '_qrcode.pdf"');
    }

    public function testPDF($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();
        $qrcodes = $this->getQrcodeInfo($event_id);

        $response = [];
        $response = [
            'voteEvent' => $voteEvent,
            'candidates' => $candidates,
            'qrcodes' => $qrcodes,
        ];

        return view('pdf.voteticket', $response);
    }

    private function getQrcodeInfo($event_id)
    {
        $qrcodes = GenerateQrcode::where('event_id', $event_id)->get();
        foreach ($qrcodes as $key => $qrcode) {
            $url = config('app.url') . 'vote/' . $event_id . '/' . $qrcode->qrcode_string; 
            $images = (new QRCode)->render($url);
            $qrcodes[$key]->qrcode_url = $images;
        }

        return $qrcodes;
    }

    public function activateVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                // 檢查是否可以啟動投票
                $this->validActivatePermission($voteEvent);

                VoteEvent::where('event_id', $validated['event_id'])
                            ->update([
                                'vote_is_ongoing' => 1,
                                'start_time' => date('Y-m-d H:i:s', time()),
                            ]);

                return response()->json(['message' => '投票活動已啟用'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    // 停用投票活動
    public function deactivateVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                $this->validDeactivatePermission($voteEvent);

                VoteEvent::where('event_id', $validated['event_id'])
                            ->update([
                                'vote_is_ongoing' => 2,
                                'end_time' => date('Y-m-d H:i:s', time()),
                            ]);

                return response()->json(['message' => '投票活動已停用'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function deleteVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                // 檢查是否可以刪除投票
                $this->validDeletePermission($voteEvent);

                VoteEvent::where('event_id', $validated['event_id'])
                            ->update(['is_delete' => 1]);

                return response()->json(['message' => '投票活動已刪除'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function lockVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                $voteEvent->is_locked = 1;
                $voteEvent->save();

                return response()->json(['message' => '投票活動已鎖定'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function unlockVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                $voteEvent->is_locked = 0;
                $voteEvent->save();

                return response()->json(['message' => '投票活動已解除鎖定'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function checkVoteSituation($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $this->addVoteStatus($voteEvent);
        $voted_qrcodes = GenerateQrcode::getVotedQrcodes($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'qrcodes' => $voted_qrcodes,
            'system_time' => date('Y-m-d H:i:s', time())
        ];

        return view('admin.votecheck', $response);
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

    public function getVoteResult($event_id)
    {
        try {
            $voteEvent = VoteEvent::find($event_id);
            $this->validGetResultPermission($voteEvent);

            $rank = Candidate::getCandidateRanking($event_id);
            foreach ($rank as &$candidate) {
                $candidate->name = $this->maskName($candidate->name);
            }

            $voteRecord = $this->getVoteRecord($event_id);

            $response = [];
            $response = [
                'vote_event' => $voteEvent,
                'rank' => $rank,
                'records' => $voteRecord
            ];
            return view('admin.voteresult', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    private function maskName($name) {
        $length = mb_strlen($name);
        $firstChar = mb_substr($name, 0, 1);
        $lastChar = mb_substr($name, -1);
        if ($length <= 2) {
            return $firstChar . '*' . $lastChar;
        }
        return $firstChar . str_repeat('*', $length - 2) . $lastChar;
    }

    private function getVoteRecord($event_id)
    {
        $results = VoteRecord::getVoteDetails($event_id);

        $groupedResults = [];

        foreach ($results as $row) {
            if (!isset($groupedResults[$row->code_id])) {
                $groupedResults[$row->code_id] = [
                    'code_id' => $row->code_id,
                    'qrcode_string' => $row->qrcode_string,
                    'updated_at' => $row->updated_at,
                    'vote' => [],
                ];
            }

            $groupedResults[$row->code_id]['vote'][] = [
                'number' => $row->number,
                'name' => $this->maskName($row->name),
                'school' => $row->school
            ];
        }

        // 按 number 排序
        foreach ($groupedResults as &$group) {
            usort($group['vote'], function ($a, $b) {
                return $a['number'] <=> $b['number'];
            });
        }

        $groupedResults = array_values($groupedResults);

        return $groupedResults;
    }

    public function exportDetail($event_id)
    {
        $eventName = VoteEvent::where('event_id', $event_id)->value('event_name');
        $voteRecord = $this->getVoteRecord($event_id);

        $response = [];
        $response = [
            'records' => $voteRecord,
            'event_name' => $eventName 
        ];
        return view('pdf.votedetail', $response);
    }

    public function adminUserList()
    {
        $adminUser = User::all();

        $response = [];
        $response = [
            'users' => $adminUser,
        ];
        return view('admin.user', $response);
    }

    public function createUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        try {
            $adminUser = new User();
            $adminUser->name = $validatedData['name'];
            $adminUser->email = $validatedData['email'];
            $adminUser->password = Hash::make($validatedData['password']);
            $adminUser->save();

            return response()->json(['message' => '新增成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '新增失敗，請重試', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteUser(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if ($user) {
            $user->delete();
            return response()->json(['message' => '使用者已刪除'], 200);
        } else {
            return response()->json(['message' => '找不到使用者'], 404);
        }
    }

    public function updateUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        $user = User::findOrFail($validatedData['user_id']);

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
