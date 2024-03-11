<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{

  public function create(Request $request)
  {

    $user =  $request->user();


    if ($user->role != 'publisher') {
      return response()->json(['status' => 'invalid rol'], 400);
    }

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'description' => 'required',
      'url' => 'required|string',
      'gender' => 'required|string',
      'author' => 'required|string',
      'reads' => 'integer',
      'author_reads' => 'integer',
      'page.*.info' => 'required',
      'page.*.page' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json($validator->errors()->toJson(), 400);
    }
    $book = Books::create(array_merge(
      $validator->validate()
    ));


    $book->pages()->createMany($request->pages);

    return response()->json([
      'message' => 'book Created',
      'book' => $book
    ], 201);
  }

  public function getbooks(Request $request)
  {
    $filter = $request->filter;

    if ($filter != 'author' && $filter != 'name' && $filter != 'gender' && $filter != 'created_at') {
      return response()->json(['status' => 'Invalid arguments in filter'], 400);
    }

    $books = Books::query()->orderBy($filter, 'asc')->get()->all();

    return response()->json($books);
  }
  public function getbook($id)
  {
    if (!$id) {
      return response()->json(['status' => 'Invalid arguments in query ID'], 400);
    }

    $book = Books::query()->with('pages')->find($id);

    $book->update(['reads' => $book->reads + 1, 'author_reads' => $book->author_reads + 1]);

    return response()->json($book);
  }
}
