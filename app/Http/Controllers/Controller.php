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
}
