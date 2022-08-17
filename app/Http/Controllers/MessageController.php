<?php

namespace App\Http\Controllers;

use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\SettingRepositoryInterface;
use App\Models\FacebookNotificationLog;
use Illuminate\Http\Request;
use Exception;
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


    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function receiveNotification(Request $request) {
    $firebaseToken = $this->settings->viewByName('firebase_token');

    try {
      // saving to log
      $log = new FacebookNotificationLog;
      $log->raw_value = json_encode($request->all());
      $log->save();

      $this->sendNotification($firebaseToken->value, 'Alert', 'Facebook Notification Received');
      return response()->json();
    } catch (Exception $e) {
      $this->er500($e->getMessage());
    }
  }

  public function verifyWebhook(Request $request) {
    try {
      // TODO: to change verify token value 'ChatTesting' to env
      /*
      if($request->hub_verify_token === env('FACEBOOK_VERIFICATION_TOKEN', 'ChatTesting')) {
        return response()->json($request->hub_challenge);
      } else {
        return response()->json('Error', 500);
      }
      */

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
    } catch(Exception $e) {
      $this->er500($e->getMessage());
    }
  }
}
