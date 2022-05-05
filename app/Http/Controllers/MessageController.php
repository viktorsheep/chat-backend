<?php

namespace App\Http\Controllers;

use App\Interfaces\MessageRepositoryInterface;
use Illuminate\Http\Request;
use Exception;

class MessageController extends Controller {
  protected $message;

  public function __construct(MessageRepositoryInterface $message) 
  {
    $this->message = $message;
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
}