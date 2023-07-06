<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function deleteUser(Request $request)
  {
    $qParams = $request->query();
    $id=$qParams["id"];

    User::where("id",$id)
            ->delete();
  }
  public function changeRole(Request $request)
  {
    $qParams = $request->query();
    $id=$qParams["id"];

    $user = User::find($id);
    $newRole=$user["role"]=="teacher"? "student":"teacher";
    User::where("id",$id)
      ->update(array('role'=>$newRole));
  }

  public function index(Request $request)
  {
    $qParams = $request->query();

    $page = $qParams["page"] ?? 1;
    $pageSize = $qParams["size"] ?? 10;
    $sort = $qParams["sort"] ?? "id";
    $order = $qParams["order"] ?? "asc";

    if ($order !== "asc" && $order !== "desc") {
      $order = "asc";
    }

    $result = $this->getUsers([
      "page" => $page,
      "pageSize" => $pageSize,
      "sort" => $sort,
      "order" => $order,
    ]);

    return response()->json($result, 200);
  }

  private function getUsers(array $params)
  {
    $query = User::whereNot("role", "admin")
      ->select("id", "first_name", "last_name", "email", "role","created_at")
      ->orderBy($params["sort"], $params["order"]);

    $numericColumns = ["id"];

    if (in_array($params["sort"], $numericColumns)) {
      $query = $query->orderBy("last_name", $params["order"]);
    }
    $students=$query->paginate($params["pageSize"], ["*"], "page", $params["page"]);
    return [
      "items" => $students->items(),
      "total" => $students->total(),
    ];
  }
}
