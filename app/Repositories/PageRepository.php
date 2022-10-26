<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\FbPage;
use App\Interfaces\PageRepositoryInterface;

class PageRepository implements PageRepositoryInterface {

  public function save($id = null, $data)
  {
    $page = $id === null
      ? new FbPage
      : FbPage::where('id', '=', $id)->first();

    foreach($data as $key => $value) {
      $page->$key = $data[$key];
    }

    $page->updated_by     = auth()->user()->id;
    $page->updated_at     = Carbon::now();

    if(is_null($id)) {
      $page->created_by = auth()->user()->id;
      $page->created_at = Carbon::now();
    }

    $page->save();

    return $page;
  }

  public function browse($with_token = false)
  {
    return $with_token === true
      ? FbPage::get()
      : FbPage::exclude(['access_token'])->get();
  }

  public function view($id, array $fields = null)
  {
    return $fields === null ? FbPage::find($id) : FbPage::find($id, $fields);
  }

  public function search($data)
  {
    return FbPage::where('name', 'like', '%' . $data . '%')->get();
  }

  public function delete($data_id)
  {
    return FbPage::where('id', '=', $data_id)->forceDelete();
  }

  public function toggleActive($id)
  {
    $page = FbPage::find($id);
    $page->is_active = $page->is_active === true ? false : true;
    $page->save();

    return $page;
  }
}
