<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FbPage extends Model
{
  protected $fillable = [
    'name',
    'url',
    'contact_person',
    'page_id',
    'is_active',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at'
  ];

  protected $hidden = [
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'created_at'  => 'date: d M Y h:i A',
    'updated_at'  => 'date: d M Y h:i A',
    'is_active'   => 'boolean'
  ];
  
  public function creator() {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function updater() {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }

  public function users() {
    return $this->belongsToMany(User::class, 'user_pages', 'page_id', 'user_id');
  }
}