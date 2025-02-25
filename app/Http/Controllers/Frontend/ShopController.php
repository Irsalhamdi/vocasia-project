<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(){

        $products = Product::query();

        if (!empty($_GET['category'])) {
            $slugs = explode(',',$_GET['category']);
            $catIds = Category::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            $products = Product::whereIn('category_id', $catIds)->paginate(3);
        }
        if (!empty($_GET['brand'])) {
            $slugs = explode(',',$_GET['brand']);
            $brandIds = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            $products = $products->whereIn('brand_id', $brandIds)->paginate(3);
        }
        else{
             $products = Product::where('status', 1)->orderBy('id','DESC')->paginate(3);
        }

        $brands = Brand::orderBy('name','ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('frontend.shop.index', compact('products', 'categories', 'brands'));
    }

    public function filter(Request $request){

        $data = $request->all();

        $catUrl = "";
        if (!empty($data['category'])) {
            foreach ($data['category'] as $category) {
              if (empty($catUrl)) {
                 $catUrl .= '&category='.$category;
              }else{
                 $catUrl .= ','.$category;
              }
           } 
        }
        $brandUrl = "";
            if (!empty($data['brand'])) {
               foreach ($data['brand'] as $brand) {
                  if (empty($brandUrl)) {
                     $brandUrl .= '&brand='.$brand;
                  }else{
                    $brandUrl .= ','.$brand;
                  }
               } 
            }
            return redirect()->route('shop.page', $catUrl.$brandUrl);
    }
}
