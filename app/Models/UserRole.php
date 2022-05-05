<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
  //
  protected $fillable = [
    'name','description'
  ];

  public function users() {
    return $this->hasMany(User::class, 'user_role_id', 'id');
  }

  /*
  public function creator() {
    return $this->belongsTo('App\Models\User', 'created_by', 'id');
  }
  */
}