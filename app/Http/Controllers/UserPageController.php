<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Interfaces\UserPageRepositoryInterface;
use Exception;

class UserPageController extends Controller {
  protected $userPage;

  public function __construct(UserPageRepositoryInterface $userPage)
  {
    $this->userPage = $userPage;
  }

  public function save($page_id, $user_id = null) {
    try {
      return $this->successResponse(
        $this->userPage->save($page_id, $user_id),
        201
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function get($user_id) {
    try {
      return $this->successResponse(
        $this->userPage->get($user_id),
        200
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function exists($page_id) {
    try {
      return $this->successResponse(
        $this->userPage->exists($page_id),
        200
      );
    } catch (Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function delete($id) {
    try {
      $this->userPage->delete($id);
      return $this->successResponse("success", 204);
    } catch (Exception $e) {
      return $this->er500($e->getMessage());
    }
  }
}
