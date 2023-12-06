<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model {
    use HasFactory;
    protected $fillable = [
        'page_id',
        'page_index_id',
        'mid',
        'psid',
        'responder_id',
        'status',
        'name',
        'has_new_message'
    ];

    public function responder() {
        return $this->belongsTo(User::class, 'responder_id', 'id');
    }

    public function client_status() {
        return $this->belongsTo(ClientStatus::class, 'status', 'id');
    }
}
