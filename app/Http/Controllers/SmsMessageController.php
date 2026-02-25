<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Jobs\SendSmsJob;
use App\Models\SmsMessage;
use Illuminate\Http\Request;

class SmsMessageController extends Controller
{

    public function send(SendSmsRequest $request){
        $project = $request->attributes->get('project');

        $phones  = array_unique($request->validated('phones'));
        $message = $request->validated('message');

        $dispatched=[];

        foreach($phones as $phone){
            $sms=SmsMessage::create([
                'project_id' => $project->id,
                'phone'      => $phone,
                'message'    => $message,
                'status'     => 'pending',
            ]);

            SendSmsJob::dispatch($sms);

            $dispatched[] = ['id' => $sms->id, 'phone' => $phone, 'status' => 'pending'];

        }

        return response()->json([
            'success' => true,
            'message' => count($dispatched) . ' SMS message(s) queued for delivery.',
            'data'    => $dispatched,
        ], 202);
    }


    public function history(Request $request){
        $project = $request->attributes->get('project');
        $perPage = min((int) $request->query('per_page', 15), 100);
        $messages = SmsMessage::where('project_id', $project->id)
            ->byStatus($request->query('status'))
            ->byPhone($request->query('phone'))
            ->byDateRange($request->query('from'), $request->query('to'))
            ->latest()
            ->paginate($perPage);

        return response()->json(['success' => true, 'data' => $messages]);

    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request,SmsMessage $smsMessage)
    {
        $project = $request->attributes->get('project');

        if($smsMessage->project_id !== $project->id){
            return response()->json(['success' => false, 'message' => 'SMS message not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $smsMessage]);
    }

}
