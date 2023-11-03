<?php

namespace App\Http\Controllers;

use App\Models\FbPage;
use App\Models\Message;
use Carbon\Carbon;
use CURLFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FacebookController extends Controller {

    public function profile($page_id) {
        $response = Http::get("https://graph.facebook.com/v14.0/$page_id");

        return response()->json($response->object(), 200);
    }

    public function conversations($page_id, Request $request) {
        try {
            $page = FbPage::where('page_id', $page_id)->first();
            $accessToken = $page->access_token;
            $limit = 25;
            $getNext = $request->query('next');

            $url = "https://graph.facebook.com/v15.0/{$page_id}/conversations";

            try {
                if ($getNext) {
                    $response = Http::get($url, [
                        'access_token' => $accessToken,
                        'limit' => $limit,
                        'fields' => 'unread_count,subject,snippet,senders,can_reply,message_count,updated_time,participants',
                        'after' => $getNext
                    ]);
                } else {
                    $response = Http::get($url, [
                        'access_token' => $accessToken,
                        'limit' => $limit,
                        'fields' => 'unread_count,subject,snippet,senders,can_reply,message_count,updated_time,participants'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json($response, 500);
            }

            if ($response->failed()) {
                $statusCode = $response->status();
                $errorResponse = $response->json();

                return response()->json([
                    'error-response' => $errorResponse,
                    'code' => $statusCode
                ], 500);
            }


            try {
                $data = $response->json();
                $conversations =  $data['data'];
            } catch (\Exception $e) {
                return response()->json([
                    'http-response-json' => $response->json(),
                    'exception' => $e
                ], 500);
            }


            $paging = $data['paging'] ?? null;
            $url = $paging['next'] ?? null;

            $next = null;
            if ($url) {
                $next = $paging['cursors']['after'];
            }


            return response()->json(['conversations' => $conversations, 'next' => $next], 200);
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function messages($page_id, $conversation_id, Request $request) {

        $page = FbPage::where('page_id', $page_id)->first();
        $limit = 25;
        $getNext = $request->query('next');

        $url = "https://graph.facebook.com/v15.0/{$conversation_id}/messages?fields=";

        if ($getNext) {
            $response = Http::get($url, [
                'access_token' => $page->access_token,
                'limit' => $limit,
                'fields' => 'id,created_time,message,from,to,tags',
                'after' => $getNext
            ]);
        } else {
            $response = Http::get("https://graph.facebook.com/v15.0/$conversation_id/messages?fields=id,created_time,message,from,to,tags&access_token=$page->access_token");
        }

        $data = $response->json();
        $messages =  $data['data'];

        $paging = $data['paging'] ?? null;
        $url = $paging['next'] ?? null;

        $next = null;
        if ($url) {
            $next = $paging['cursors']['after'];
        }

        return response()->json(['messages' => $messages, 'next' => $next], 200);
    }

    public function conversation($page_id, $conversation_id) {

        $page = FbPage::where('page_id', $page_id)->first();

        $response = Http::get("https://graph.facebook.com/v15.0/$conversation_id?fields=unread_count,subject,snippet,senders,can_reply,message_count,updated_time,participants&access_token=$page->access_token");

        return response()->json($response->json(), 200);
    }

    public function attachments($page_id, $conversation_id) {

        $page = FbPage::where('page_id', $page_id)->first();

        $response = Http::get("https://graph.facebook.com/v15.0/$conversation_id/attachments?fields=id,mime_type,name,size,file_url,image_data&access_token=$page->access_token");

        return response()->json($response->object(), 200);
    }

    public function sendMessage(Request $req) {
        $recipient_id = $req->recipient_id;
        $message = $req->message;
        $access_token = $req->access_token;
        $last_date = $req->last_date;

        $lastDateCarbon = Carbon::parse($last_date);

        $currentDate = Carbon::now();

        // $response = Http::post(
        //     "https://graph.facebook.com/v15.0/me/messages?recipient={id:$recipient_id}&messaging_type=RESPONSE&message={'text':'$message','tag':'HUMAN_AGENT'}&access_token=$access_token"
        // );
        $tag = '';

        if ($lastDateCarbon->diffInHours($currentDate) < 24) {
            Http::post(
                "https://graph.facebook.com/v15.0/me/messages?recipient={id:$recipient_id}&messaging_type=RESPONSE&message={'text':'$message'}&access_token=$access_token"
            );
            $tag = 'none';
        } elseif ($lastDateCarbon->diffInDays($currentDate) < 7) {
            Http::post(
                "https://graph.facebook.com/v15.0/me/messages?access_token=$access_token",
                [
                    'recipient' => [
                        'id' => $recipient_id,
                    ],
                    'message' => [
                        'text' => $message,
                    ],
                    'tag' => 'HUMAN_AGENT',
                    'messaging_type' => 'MESSAGE_TAG',
                ]
            );
            $tag = 'HUMAN_AGENT';
        } else {
            Http::post(
                "https://graph.facebook.com/v15.0/me/messages?access_token=$access_token",
                [
                    'recipient' => [
                        'id' => $recipient_id,
                    ],
                    'message' => [
                        'text' => $message,
                    ],
                    'tag' => 'POST_PURCHASE_UPDATE',
                    'messaging_type' => 'MESSAGE_TAG',
                ]
            );
            $tag = 'POST_PURCHASE_UPDATE';
        }


        return response()->json([
            'message' => $message,
            'tag' => $tag
        ], 200);
    }

    public function sendVoice(Request $request) {
        try {
            $last_date = $request->last_date;
            $lastDateCarbon = Carbon::parse($last_date);
            $currentDate = Carbon::now();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v15.0/me/messages?access_token=$request->access_token");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type' => 'multipart/form-data']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            if ($lastDateCarbon->diffInHours($currentDate) < 24) {
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
                $tag = 'none';
            } elseif ($lastDateCarbon->diffInDays($currentDate) < 7) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'recipient' => $request->recipient,
                    'message' => json_encode([
                        'attachment' => [
                            'type' => 'audio',
                            'payload' => ['is_reusable' => false]
                        ]
                    ]),
                    'filedata' => new CURLFile($request->file('filedata'), 'audio/webm'),
                    'tag' => 'HUMAN_AGENT',
                    'messaging_type' => 'MESSAGE_TAG',
                ]);
                $tag = 'HUMAN_AGENT';
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'recipient' => $request->recipient,
                    'message' => json_encode([
                        'attachment' => [
                            'type' => 'audio',
                            'payload' => ['is_reusable' => false]
                        ]
                    ]),
                    'filedata' => new CURLFile($request->file('filedata'), 'audio/webm'),
                    'tag' => 'POST_PURCHASE_UPDATE',
                    'messaging_type' => 'MESSAGE_TAG',
                ]);
                $tag = 'POST_PURCHASE_UPDATE';
            }



            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return response(curl_error($ch));
            }
            curl_close($ch);

            return response()->json([
                'FB api response' => $response,
                'tag' => $tag
            ], 200);
        } catch (Exception $e) {
            return response($e, 500);
        }
    }
}
