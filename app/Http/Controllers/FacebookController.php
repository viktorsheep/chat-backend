<?php

namespace App\Http\Controllers;

use CURLFile;
use GuzzleHttp\Client;
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

    public function sendMessage(Request $req) {
        $recipient_id = $req->recipient_id;
        $message = $req->message;
        $access_token = $req->access_token;

        // return response()->json($filedata, 200);

        // Http::attach('file', file_get_contents($filedata), 'myfile.wav')->withHeaders([
        //     'Content-Type' => 'form-data',
        // ])->post('example.org')->json();

        $response = Http::post(
            "https://graph.facebook.com/v15.0/me/messages?recipient={id:$recipient_id}&message={'text':'$message'}&access_token=$access_token"
        );

        return response()->json([
            'api_response' => $response->object(),
            'recipient_id' => $recipient_id,
            'message' => $message,
            'token' => $access_token
        ], 200);
    }

    public function sendVoice(Request $req) {
        // $filedata = $req->file('filedata');
        $filedata = $req->filedata;
        // $filedata = fopen($filedata, 'r');
        $recipient_id = "$req->recipient_id";
        $access_token = $req->access_token;

        //  $req->file('filedata');
        // $ext = $filedata->getClientOriginalExtension();
        // $ext = 'wav';
        // $fileQuery = '@' . $filedata . '.' . $ext . ';type=audio/' . $ext;
        // $fileQuery = $filedata;
        // $fileQuery = file_get_contents($filedata);

        $url = 'https://graph.facebook.com/v15.0/me/messages';
        // $queryString = "?recipient={id:" . $req->recipient_id . "}&message={'attachment':{'type':'audio'}}&filedata=" . $fileQuery . "&access_token=" . $req->access_token;

        // $response = Http::withHeaders([
        //     'Content-Type' => 'multipart/form-data'
        // ])->post(
        //     $url . $queryString
        //     // "https://graph.facebook.com/v15.0/me/messages?recipient={id:5258000234291901}&message={'attachment':{'type':'audio'}}&filedata=$req->filedata&access_token=$req->access_token"
        // );
        try {
            // $f = file_get_contents($filedata);

            // $response = Http::attach('recipient', $recipient_id)
            //     ->attach('message', $message)
            //     ->attach('filedata', $filedata)
            //     ->withHeaders([
            //         'Content-Type' => 'multipart/form-data',
            //     ])->post($url . '?access_token=' . $access_token);

            $url = "https://graph.facebook.com/v15.0/100614572669575/messages?recipient={id:$recipient_id}&message={'attachment':{'type':'audio'}}&filedata='@$filedata.wav'&access_token=$access_token";

            // $response = Http::attach('filedata', $filedata)
            //     ->withHeaders([
            //         'Content-Type' => 'audio/wav',
            //     ])->post("https://graph.facebook.com/v15.0/100614572669575/messages?recipient={id:$recipient_id}&message={'attachment':{'type':'audio'}}&filedata='@$filedata.wav'&access_token=$access_token");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v15.0/me/messages?access_token=$access_token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'recipient' => "{'id':'$recipient_id'}",
                'message' => '{"attachment":{"type":"audio", "payload":{"is_reusable":true}}}',
                'filedata' => new CURLFile($filedata),
            ]);

            $response = curl_exec($ch);

            return response()->json([
                'api response json' => $response,
                'url' => $url
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'exception' => $e,
                'api response json' => $response,
            ], 500);
        }
    }
}
