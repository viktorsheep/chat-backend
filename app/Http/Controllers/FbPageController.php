<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Interfaces\PageRepositoryInterface;
use Exception;

class FbPageController extends Controller
{
  protected $page;

  public function __construct(PageRepositoryInterface $page) {
    $this->page = $page;
  }

  public function add(Request $request) {
    $this->validate($request, [
      'name'            => 'required',
      'url'             => 'required',
      'contact_person'  => 'required',
      'access_token'    => 'required',
      'page_id'         => 'required',
      'is_active'       => 'required|boolean'
    ]);

    $data =$request->only([
      'name', 'url', 'contact_person', 'access_token', 'is_active', 'page_id'
    ]);

    try {
      return $this->successResponse(
        $this->page->save(null, $data),
        201
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function browse(Request $request) {
    try {
      return $this->successResponse(
        $this->page->browse($request->with_token === 'true' ? true : false),
        200
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function view($id) {
    try {
      return $this->successResponse(
        $this->page->view($id),
        200
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function edit($id, Request $request) {
    $this->validate($request, [
      'name'            => 'required',
      'url'             => 'required',
      'contact_person'  => 'required',
      'page_id'         => 'required',
      'is_active'       => 'required|boolean'
    ]);

    $data = $request->only([
      'name',
      'url',
      'contact_person',
      'page_id',
      'is_active'
    ]);

    try {
      return $this->successResponse(
        $this->page->save($id, $data),
        201
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function updateAccessToken($id, Request $request) {
    $this->validate($request, [
      'access_token'  => 'required'
    ]);

    $data = $request->only([
      'access_token'
    ]);

    try {
      return $this->successResponse(
        $this->page->save($id, $data),
        201
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function getAccessToken($id) {
    try {
      return $this->successResponse(
        $this->page->view($id, ['access_token']),
        200
      );
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }

  public function toggleActive($id) {
    try {
      $this->page->toggleActive($id);
    } catch(Exception $e) {
      return $this->er500($e->getMessage());
    }
  }
}