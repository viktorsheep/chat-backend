<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookNotificationLog extends Model
{
  protected $fillable = [
    'raw_value',
    'created_at',
    'updated_at'
  ];

  protected $casts = [
    'created_at'  => 'date:d M Y h:i A',
    'updated_at'  => 'date:d M Y h:i A',
  ];
}