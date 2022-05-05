<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\Message;
use App\Interfaces\MessageRepositoryInterface;
use Exception;

class MessageRepository implements MessageRepositoryInterface {

  public function save($id = null, $data) {

    try {
      $msg = $id === null
        ? new Message
        : Message::where('id', '=', $id)->first();

      $msg->message = $data['message'];
      $msg->message_type_id = $data['message_type_id'];
      $msg->user_id = $data['user_id'];
      $msg->fb_page_id =$data['fb_page_id'];
      
      if(is_null($id)) {
        $msg->created_at = Carbon::now();
      }

      $msg->updated_at = Carbon::now();
      $msg->save();

      return $msg;
    } catch(Exception $e) {
      return $e;
    }
  }

  public function browse($user_id, $fb_page_id) {
    // TODO : Make pagination
    return Message
      ::where('user_id', '=', $user_id)
      ->where('fb_page_id', '=', $fb_page_id)
      ->get();
  }

  public function view($id) {
    return Message::find($id);
  }

  public function search($data) {
    return Message::where('name', 'like', '%' . $data . '%')->get();
  }

  public function delete($data_id) {
    return Message::where('id', '=', $data_id)->forceDelete();
  }
}
