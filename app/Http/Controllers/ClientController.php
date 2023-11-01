<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Exception;
use FFMpeg\Media\Clip;
use Illuminate\Http\Client\ResponseSequence;

class ClientController extends Controller {
    // set
    public function set(Request $request) {
        try {
            if ($request->conversations) {
                foreach ($request->conversations as $c) {
                    $participants = $c['participants']['data'];

                    $foundParticipant = collect($participants)->first(function ($participant) use ($request) {
                        return $participant['id'] !== $request->page_id;
                    });

                    $psid = $foundParticipant ? $foundParticipant['id'] : null;

                    $client = Client::updateOrCreate([
                        'page_index_id' => $request->page_index_id,
                        'page_id' => $request->page_id,
                        'mid' => $c['id'],
                        'psid' => $psid
                    ], [
                        'page_index_id' => $request->page_index_id,
                        'page_id' => $request->page_id,
                        'mid' => $c['id'],
                        'psid' => $psid
                    ]);
                }
                return response()->json(Client::all(), 201);
            } else {
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
            }
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    } // e.o set

    // set status
    public function setStatus($id, $status_id) {
        $client = Client::where('id', $id)->first();

        $client->status = $status_id;
        $client->update();

        return response()->json($client, 200);
    }

    // set responder
    public function setResponder($client_psid, $responder_id) {
        $client = Client::where('psid', $client_psid)->first();

        $client->responder_id = $responder_id;
        $client->update();

        return response()->json($client, 200);
    }

    // get data
    public function getData($client_mid) {
        try {
            $client = Client::where('mid', $client_mid)->with('responder', 'status')->first();
            return response()->json($client, 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    // get by sender id
    public function getBySenderId($sender_id, $page_id) {
        try {
            return response()->json(Client::where('psid', $sender_id)->where('page_id', $page_id)->first(), 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
