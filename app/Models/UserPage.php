<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPage extends Model
{
  protected $fillable = [
    'user_id',
    'page_id',
    'is_joined',
    'joined_date',
    'left_date',
    'created_at',
    'updated_at'
  ];

  protected $casts = [
    'joined_date'  => 'date:d M Y h:i A',
    'left_date'  => 'date:d M Y h:i A',
    'created_at'  => 'date:d M Y h:i A',
    'updated_at'  => 'date:d M Y h:i A',
    'is_joined' => 'boolean'
  ];
}