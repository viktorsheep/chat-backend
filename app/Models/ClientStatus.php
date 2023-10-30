<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientStatus extends Model {
    protected $fillable = [
        'name'
    ];

    public function clients() {
        return $this->hasMany(Client::class, 'status', 'id');
    }
}
