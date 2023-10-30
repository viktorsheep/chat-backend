<?php

namespace App\Http\Controllers;

use App\Models\ClientStatus;
use Illuminate\Http\Request;

class ClientStatusController extends Controller {
    // get
    public function getClientStatus() {
        return response()->json(ClientStatus::all(), 200);
    }

    // get by id
    public function getById($id) {
        return response()->json(ClientStatus::where('id', $id)->with('clients')->get(), 200);
    }
}
