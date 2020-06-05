<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Article;
use Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class searchController extends Controller
{
    public function search(Request $request)
    {
    	$validator=Validator::make($request->all(), [
            'search'=>'required',
            'tag'=>'required',
            //'body'=>'required'
        ]);
        if ($validator->fails())
        {
            return response(array(
                'success'=>3,
                'data'=>$validator->errors()
            ));
        }
        else
        {
        	$perPage=4;
        	$search = $request->search;
        	// $body = $request->body;
        	//$data = Article::searchByQuery(['multi_match' => ['title' => $title]]);
        	$article = Article::searchByQuery([
                'multi_match' => [
                'query' => $search,
                'fields' => [ "title^5", "body"]
                ],
            ]);
            
              $currentPage = LengthAwarePaginator::resolveCurrentPage();
              $productCollection = collect($article);
              $currentPageproducts = $productCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
              $paginatedproducts= new LengthAwarePaginator($currentPageproducts , count($productCollection), $perPage);
              $paginatedproducts->setPath($request->url());
              $data=$paginatedproducts;
        }
        if($data)
        {
            return response(array(
              'success'=>1,
              'data'=>$data,
            ),Response::HTTP_OK);
        }
        else
        {
            return response(array(
               'success'=>0,
               'msg'=>'Something Went Wrong'
            ),Response::HTTP_OK);
        }
    }
}
