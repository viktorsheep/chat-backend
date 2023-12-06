<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Validation\Rules\Exists;

class UserController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $user;

    public function __construct(UserRepositoryInterface $user) {
        $this->user = $user;
        $this->userAddress = 'UserAddress';
    }

    public function add(Request $request) {
        $this->validate($request, [
            'name'          => 'required|string',
            'email'         => 'required|string|unique:users',
            'password'      => 'required|confirmed',
            'is_active'     => 'required|boolean',
            'is_confirmed'  => 'required|boolean',
            'user_role_id'  => 'required|integer'
        ]);

        try {
            $result = $this->user->add($request);
            return $this->successResponse($result, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    public function browse() {
        try {
            $result = $this->user->browse();
            return $this->successResponse($result, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    public function admins() {
        try {
            $result = $this->user->admins();
            return $this->successResponse($result, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    public function users() {
        try {
            return $this->successResponse(
                $this->user->users(),
                200
            );
        } catch (Exception $e) {
            return $this->er500($e->getMessage());
        }
    }

    public function roles() {
        try {
            $result = $this->user->roles();
            return $this->successResponse($result, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    public function view($id) {
        try {
            $result = $this->user->view($id);
            return $this->successResponse($result, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    public function edit($id, Request $request) {
        $data = $request->only([
            'name',
            'phone',
            'address',
            'email',
            'password',
            'user_role_id'
        ]);


        try {
            $result = $this->user->edit($id, $data);
            return $this->successResponse($result, 200);
        } catch (Exception $e) {
            return $this->successResponse($e->getMessage(), 500);
        }
    }

    public function search(Request $request) {
        $data = $this->user->search($request->name);
        return $this->successResponse($data, 200);
    }

    public function delete($id) {
        $this->user->delete($id);
        return $this->successResponse('deleted successfully.', 200);
    }

    public function confirm($id) {
        try {
            return response()->json($this->user->confirm($id));
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function messages() {
    }

    public function updateFirebaseToken($id, Request $request) {
        try {
            return $this->successResponse(
                $this->user->updateFirebaseToken($id, $request->firebase_token),
                200
            );
        } catch (Exception $e) {
            return $this->er500($e->getMessage(), 500);
        }
    }

    /*
  public function updateImage($id, Request $request)
  {
    $image = $request->file('file');
    $ext = $image->getClientOriginalExtension();
    $name = 'pd_' . uniqid() . '.' . $ext;
    $path = 'images' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    $request->file('file')->move($path, $name);
    $file = $path. $name;

    try{
      $result = $this->product->updateImage($id, $file);
        return response()->json([
          'data' => $result,
          'result' => 'successfully updated image'
        ], 200);
    } catch(Exception $e){
        return response()->json(['data' => $e], 500);
    }
  }

  public function deleteImage($id)
  {
    try{
      $this->product->deleteImage($id);
      return response()->json([
        'result' => 'successfully deleted image'
      ], 200);
    } catch(Expection $e){
      return response()->json(['data' => $e], 500);
    }
  }
  */
}
