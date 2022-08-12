<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Exception;

class Controller extends BaseController {
  public function successResponse($data = null, $status = 200) {
    return $data === null ? response('', 204)
      : response()->json( $data ,$status);
  }

  public function errorResponse(Exception $e, $status = 500) {
    $response = [
      'message' => $e->getMessage()
    ];

    if (config('app.debug')) {
      $response['code'] = $e->getCode();
      $response['line'] = $e->getLine();
      $response['file'] = $e->getFile();
      $response['trace'] = $e->getTrace();
      $response['e'] = $e;
    }

    return response()->json($response, $status);
  }

  public function er500($message, $status = 500) {
    return response()->json(["error" => $message], $status);
  }

  public function sendNotification(array $firebaseToken, $title, $body) {
    $serverAPIKey = env('FIREBASE_API_KEY', false);
    if($serverAPIKey !== null || $serverAPIKey !== '') {

      $data = json_encode([
        "registration_ids"  => $firebaseToken,
        "notification"      => [
            "title"               => $title,
            "body"                => $body,
            "content_available"   => true,
            "priority"            => "high"
          ]
        ]);

        $headers = [
          'Authorization: key=' . $serverAPIKey,
          'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        curl_close ( $ch );

        return true;
    } else {
      return false;
    }
  }
}
