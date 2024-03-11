<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{

  /**
   * create a book if user have 'publisher' role
   * this also create pages releated to a book, this should be a transacction
   * @return \Illuminate\Http\JsonResponse
   */

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
  /**
   * Get books with any specified filter
   * 'author' 'name''gender' 'created_at'
   * @return \Illuminate\Http\JsonResponse
   */
  public function getbooks(Request $request)
  {
    $filter = $request->filter;

    if ($filter != 'author' && $filter != 'name' && $filter != 'gender' && $filter != 'created_at') {
      return response()->json(['status' => 'Invalid arguments in filter'], 400);
    }

    $books = Books::query()->orderBy($filter, 'asc')->get()->all();

    return response()->json($books);
  }

  /**
   * Get a book with his pages
   * Increase the reads count property in the book to use it in the statistics
   * @return \Illuminate\Http\JsonResponse
   */

  public function getbook($id)
  {
    if (!$id) {
      return response()->json(['status' => 'Invalid arguments in query ID'], 400);
    }

    $book = Books::query()->with('pages')->find($id);

    $book->update(['reads' => $book->reads + 1, 'author_reads' => $book->author_reads + 1]);

    return response()->json($book);
  }

  /**
   * Get Books statistics
   * Most readed book, less readed book and most consulted author
   * @return \Illuminate\Http\JsonResponse
   */

  public function statistics()
  {

    $topReads = Books::orderByRaw("CAST(`reads` as UNSIGNED) DESC")->limit(2)->get();

    $lessReads = Books::orderByRaw("CAST(`reads` as UNSIGNED) ASC")->limit(2)->get();

    $topAuthors =  Books::select('author', DB::raw('SUM(`reads`) as total_reads'))
      ->groupBy('author')
      ->orderBy('total_reads', 'DESC')
      ->limit(2)
      ->get();


    return response()->json([
      'top_reads' => $topReads,
      'less_reads' => $lessReads,
      'top_authors' => $topAuthors,
    ]);
  }
}
