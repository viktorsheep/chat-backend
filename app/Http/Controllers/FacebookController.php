<?php

namespace App\Http\Controllers;

use CURLFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookController extends Controller {

    public function profile($page_id) {
        $response = Http::get("https://graph.facebook.com/v14.0/$page_id");

        return response()->json($response->object(), 200);
    }

    public function conversations($page_id, $access_token) {

        $response = Http::get("https://graph.facebook.com/v15.0/$page_id/conversations?fields=unread_count,subject,snippet,senders,can_reply,message_count,updated_time,participants&access_token=$access_token");

        return response()->json($response->object(), 200);
    }

    public function messages($page_id, $access_token) {

        $response = Http::get("https://graph.facebook.com/v15.0/$page_id/messages?fields=id,created_time,message,from,to,tags&access_token=$access_token");

        return response()->json($response->object(), 200);
    }

    public function attachments($page_id, $access_token) {

        $response = Http::get("https://graph.facebook.com/v15.0/$page_id/attachments?fields=id,mime_type,name,size,file_url&access_token=$access_token");

        return response()->json($response->object(), 200);
    }

    public function sendMessage(Request $req) {
        $recipient_id = $req->recipient_id;
        $message = $req->message;
        $access_token = $req->access_token;

        $response = Http::post(
            "https://graph.facebook.com/v15.0/me/messages?recipient={id:$recipient_id}&messaging_type=RESPONSE&message={'text':'$message'}&access_token=$access_token"
        );

        return response()->json([
            'api_response' => $response->object(),
            'recipient_id' => $recipient_id,
            'message' => $message,
            'token' => $access_token
        ], 200);
    }

    public function sendVoice(Request $request) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v15.0/me/messages?access_token=$request->access_token");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type' => 'multipart/form-data']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'recipient' => $request->recipient,
                'message' => json_encode([
                    'attachment' => [
                        'type' => 'audio',
                        'payload' => ['is_reusable' => false]
                    ]
                ]),
                'filedata' => new CURLFile($request->file('filedata'), 'audio/webm')
            ]);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return response(curl_error($ch));
            }
            curl_close($ch);

            // $test = $this->handOver($request);

            return response()->json([
                'FB api response' => $response,
                // 'Recipient ID' => $test,
                'token' => $request->access_token
            ], 200);
        } catch (Exception $e) {
            return response($e, 500);
        }
    }
}
