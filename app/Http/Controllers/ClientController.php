<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Client\ResponseSequence;

class ClientController extends Controller {
    // set
    public function set(Request $request) {
        try {
            $client = Client::updateOrCreate([
                'page_index_id' => $request->page_index_id,
                'page_id' => $request->page_id,
                'mid' => $request->mid,
                'psid' => $request->psid
            ], [
                'page_index_id' => $request->page_index_id,
                'page_id' => $request->page_id,
                'mid' => $request->mid,
                'psid' => $request->psid
            ]);

            return response()->json($client, 201);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    } // e.o set

    // get by sender id
    public function getBySenderId($sender_id) {
        try {
            return response()->json(Client::where('psid', $sender_id)->first(), 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
