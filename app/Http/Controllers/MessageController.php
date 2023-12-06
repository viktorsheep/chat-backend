<?php

namespace App\Http\Controllers;

use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\SettingRepositoryInterface;
use App\Models\FacebookNotificationLog;
use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;


class MessageController extends Controller {
    protected $message, $settings;

    public function __construct(
        MessageRepositoryInterface $message,
        SettingRepositoryInterface $settings
    ) {
        $this->message = $message;
        $this->settings = $settings;
        $this->token = env('FACEBOOK_VERIFICATION_TOKEN', 'ChatTesting');
    }

    public function browse($fb_page_id) {
        try {
            $result = $this->message->browse(auth()->user()->id, (int) $fb_page_id);

            return $this->successResponse(
                $result,
                200
            );
        } catch (Exception $e) {
            $this->er500($e->getMessage());
        }
    }

    public function getAudio(Request $req) {
        try {
            $response = Http::sink(base_path('public') . DIRECTORY_SEPARATOR . 'asdf.webm')
                ->withHeaders(['Content-Type' => 'audio/webm'])
                ->get($req->url);
            try {
                $af = file_get_contents(base_path('public') . DIRECTORY_SEPARATOR . 'asdf.webm');
                $cv = base64_encode($af);
            } catch (Exception $e) {
                return response()->json($e->getMessage(), 500);
            }

            return response()->json(['blob' => $cv], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function add($page_id, Request $request) {
        try {

            $request->message_type_id = auth()->user()->user_role_id === 3 ? 1 : 2;
            $request->user_id = auth()->user()->id;
            $request->fb_page_id = $page_id;

            /*
      $data = $request->only([
        'message',
      ]);
      */

            $data['message'] = $request->message;
            $data['message_type_id'] = auth()->user()->user_role_id === 3 ? 1 : 2;
            $data['user_id'] = auth()->user()->id;
            $data['fb_page_id'] = (int) $page_id;

            $msg = $this->message->save(null, $data);

            return response()->json($msg, 201);

            /*
      return $this->successResponse(
        $this->message->save(
          null,
          $request->only([
            'message',
            'message_type_id',
            'user_id',
            'fb_page_id'
          ])
        ),
        201
      );
      */

            return response()->json(['hi']);


            // |*****************************|
            // |TODO: Fire message sent event|
            // |*****************************|


        } catch (Exception $e) {
            return $this->er500($e->getMessage());
        }
    }

    public function receiveNotification(Request $request) {
        // $firebaseToken = $this->settings->viewByName('firebase_token');
        $log = new FacebookNotificationLog;

        try {
            // saving to log
            $log->page_id = $request->entry[0]['id'];

            $log->raw_value = json_encode($request->all());
            $log->save();
            return response()->json();
        } catch (Exception $e) {
            $log->raw_value = json_encode($e->getMessage());
            $this->er500($e->getMessage());
            $log->save();
        }
    }

    public function noti($page_id) {
        try {
            $response = new StreamedResponse(function () use ($page_id) {
                while (true) {
                    $log = FacebookNotificationLog::where('page_id', $page_id)->orderBy('created_at', 'desc')->first();
                    $data = $log === null ? $page_id : $log->raw_value;
                    echo 'data: ' . $data . "\n\n";
                    ob_flush();
                    flush();
                    usleep(5000000);
                }
            });
            $response->headers->set('Content-Type', 'text/event-stream');
            $response->headers->set('X-Accel-Buffering', 'no');
            $response->headers->set('Cach-Control', 'no-cache');
            return $response;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function verifyWebhook(Request $request) {
        try {
            $log = new FacebookNotificationLog;
            $log->raw_value = json_encode($request->all());
            $log->save();

            $mode  = $request->get('hub_mode');
            $token = $request->get('hub_verify_token');

            if ($mode === "subscribe" && $this->token and $token === $this->token) {
                return response($request->get('hub_challenge'));
            }

            return response("Invalid token!", 400);
        } catch (Exception $e) {
            $this->er500($e->getMessage());
        }
    }

    public function uploadAudio(Request $request) {
        $this->validate($request, [
            'file' => 'required|max:50000'
        ]);

        $name = 'ac_' . uniqid() . '.wav';
        $path = 'files' . DIRECTORY_SEPARATOR;
        $request->file('file')->move($path, $name);
        $file_name = $path . $name;

        return response()->json(['file_name' => $file_name]);
    }

    public function deleteAudio(Request $request) {
        $this->validate($request, [
            'file_path' => 'required'
        ]);

        $file = public_path($request->file_path);

        try {
            File::delete($file);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            $this->er500($e->getMessage());
        }
    }
}
