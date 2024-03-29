<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Exception;

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
                        'psid' => $psid,
                    ], [
                        'page_index_id' => $request->page_index_id,
                        'page_id' => $request->page_id,
                        'mid' => $c['id'],
                        'psid' => $psid,
                    ]);
                }
                return response()->json(Client::all(), 201);
            } else {
                $client = Client::updateOrCreate([
                    'page_index_id' => $request->page_index_id,
                    'page_id' => $request->page_id,
                    'mid' => $request->mid,
                    'psid' => $request->psid,
                ], [
                    'page_index_id' => $request->page_index_id,
                    'page_id' => $request->page_id,
                    'mid' => $request->mid,
                    'psid' => $request->psid,
                ]);
                return response()->json($client, 201);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    } // e.o set

    // set status
    public function setStatus($client_mid, $status_id) {
        try {
            $client = Client::where('mid', $client_mid)->first();

            $client->status = $status_id;
            $client->update();

            return response()->json(Client::where('mid', $client_mid)->with('responder', 'client_status')->first(), 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    // set responder
    public function setResponder($client_psid, $responder_id) {
        $client = Client::where('psid', $client_psid)->first();

        $client->responder_id = $responder_id;
        $client->update();

        return response()->json(Client::where('psid', $client_psid)->with('responder', 'client_status')->first(), 200);
    }

    // read message
    public function readMessage($client_mid) {
        $client = Client::where('mid', $client_mid)->first();

        $client->has_new_message = false;
        $client->update();

        return response()->json(Client::where('mid', $client_mid)->with('responder', 'client_status')->first(), 200);
    }

    // update additional_information
    public function updateAdditionalInformation($client_mid, Request $request) {
        $client = Client::where('mid', $client_mid)->first();

        $client->has_new_message = false;
        $client->additional_information = $request->info;
        $client->update();

        return response()->json(Client::where('mid', $client_mid)->with('responder', 'client_status')->first(), 200);
    }

    // get data
    public function getData($client_mid, Request $request) {
        try {
            $client = Client::where('mid', $client_mid)->with('responder', 'client_status')->first();

            if (!$client->name) {
                $client->name = $request->name;
                $client->update();
            }
            if ($client->additional_information !== $request->info) {

                if ($client->additional_information !== null) {
                    $stored = json_decode($client->additional_information);
                    $new = json_decode($request->info);
                    if ($stored->snippet !== $new->snippet) {
                        $client->has_new_message = true;
                    }
                }
                $client->additional_information = $request->info;
                $client->update();
            }

            return response()->json(
                Client::where('mid', $client_mid)->with('responder', 'client_status')->first(),
                200
            );
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    // get client
    public function getClient($client_mid) {
        try {
            $client = Client::where('mid', $client_mid)->with('responder', 'client_status')->first();

            return response()->json($client, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    // get by sender id
    public function getBySenderId($sender_id, $page_id) {
        try {
            return response()->json(Client::where('psid', $sender_id)->where('page_id', $page_id)->first(), 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    // get by filteredData
    public function filteredData($page_id, Request $request) {
        try {

            $query = Client::where('page_id', $page_id);

            if (!empty($request->responders)) {
                $query->whereIn('responder_id', $request->responders);
            }

            if (!empty($request->statuses)) {
                $query->whereIn('status', $request->statuses);
            }
            $result = $query->with('client_status', 'responder')->paginate(1);

            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
