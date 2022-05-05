<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  protected $fillable = [
    'message',
    'message_type_id',
    'user_id',
    'fb_page_id',
    'created_at',
    'updated_at'
  ];

  protected $hidden = [
    'user_id',
    'fb_page_id'
  ];

  protected $casts = [
    'created_at'  => 'date: d M Y h:i A',
    'updated_at'  => 'date: d M Y h:i A',
  ];

  public function messageType() {
    return $this->belongsTo(MessageType::class, 'message_type_id', 'id');
  }

  public function user() {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function page() {
    return $this->belongsTo(FbPage::class, 'fb_page_id', 'id');
  }
}