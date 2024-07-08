<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Replacement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\AlternativeProduct;
use App\Models\SubstituteProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function addProduct()
    {
        $product = Product::select('id','productname','part_no')->get();
        return view("admin.product.addproduct", compact('product'));
    }

  public function storeProduct(Request $request)
  {

    $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    if($validator->fails()) {
          Session::put('warning', 'Already warned you to use jpeg,png,jpg,gif format and max size 2048 KB !');
          return back();
    }

    $check = Product::where('part_no', $request->part_no)->first();
    if ($check) {
        if ($check->category_id == $request->pcategoryselect) {
            Session::put('warning', 'This Part no Already Exist !');
            return back();
        }
    }

    $image = $request->image;

    $product = new Product();
    $product->productname = $request->product;
    $product->part_no = $request->part_no;
    $product->category_id = $request->pcategoryselect;
    $product->brand_id = $request->pbrandselect;
    $product->branch_id = Auth::user()->branch_id;
    $product->group_id = $request->group_id;
    $product->unit = $request->unit;
    $product->model = $request->model;
    $product->location = $request->location;
    $product->replacement = $request->replacement;
    $product->vat_percent = $request->vat_percent;
    $product->vat_amount = $request->sell_price * ($request->vat_percent/100);
    $product->selling_price = $request->sell_price;
    $product->selling_price_with_vat = $request->sell_price + $request->sell_price * ($request->vat_percent/100);
    $product->description = $request->productdesc;
    $product->created_by = Auth::user()->id;
    
    if ($image) {
    	$rand = mt_rand(100000, 999999);
      $imageName = time(). $rand .'.'.$request->image->extension();
      $request->image->move(public_path('images/product'), $imageName);
      $product->image= $imageName;
    }
    
    if ($product->save()) {
        if ($request->input('alternative')) {
          foreach($request->input('alternative') as $key => $value)
          {
              $alt = new AlternativeProduct();
              $alt->product_id = $product->id;
              $alt->alternative_product_id = $value;
              $alt->created_by = Auth::user()->id;
              $alt->save();
          }
        }

        if ($request->replacement) {

            $allreplacementid = explode(',',$request->replacement);

            foreach($allreplacementid as $key => $value)
            {
                $replace = new Replacement();
                $replace->product_id = $product->id;
                $replace->replacementid = $value;
                $replace->created_by = Auth::user()->id;
                $replace->save();
            }
        }
    }
    Session::put('success', 'New Product Saved Successfully !');
    return back();
  }



  public function view_manage_product()
  {
    $product = Product::all();
    return view("admin.product.manageproduct", compact('product'));
  }

  public function filter_product(Request $request)
  {
      $category = request('category');
      $brand = request('brand');
      $products = Product::with('brand','category');

      if($category){
          $products = $products->whereHas('category',function($query) use($category){
              $query->where('category_id',$category);
          });

      }
      if($brand){

          $products = $products->whereHas('brand',function($query) use($brand){
              $query->where('brand_id',$brand);
          });
      }
      return Datatables::of($products)
          ->addColumn('action', function ($product) {
              $btn = '<div class="table-actions">';

            //   if (Auth::user()) {
            //       $btn .= "<button data-toggle='modal' data-target='#editproductModal' class='btn btn-sm btn-primary edit-btn' value='$product->id'><i class='fa fa-pencil'></i> Edit</button>";
            //   }

              if (Auth::user()) {
                $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/admin/product-edit/'.$product->id.'" class="btn btn-sm btn-primary"><span title="Return"><i class="fa fa-pencil"></i>Edit</span></a>';
            }

              $btn .= '</div>';
              return $btn;
          })
          ->rawColumns(['action'])
          ->make(true);
    }

    public function editProduct($id)
    {
        $product = Product::with('brand','size','category','alternativeproduct','group','replacement')->where('id',$id)->first();
        // dd($product);
        $alternatives = Product::all();
        return view("admin.product.editproduct", compact('product','alternatives'));
    }

    public function get_product(Product $product){
        return $product->load('brand','size','category','alternativeproduct','group','replacement');
    }

    public function update_product_details(Request $request)
    {
      $data = $request->data;
    //   $product = Product::where('id',$data['id'])
    //             ->update($data);


    $image = $request->image;

    $product = Product::find($request->id);
    $product->productname = $request->productname;
    $product->part_no = $request->part_no;
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id;
    $product->group_id = $request->group;
    $product->unit = $request->unit;
    $product->model = $request->model;
    $product->location = $request->location;
    $product->replacement = $request->replacement;
    $product->vat_percent = $request->vat_percent;
    $product->vat_amount = $request->selling_price * ($request->vat_percent/100);
    $product->selling_price = $request->selling_price;
    $product->selling_price_with_vat = $request->selling_price + $request->selling_price * ($request->vat_percent/100);
    $product->description = $request->description;
    
    if ($image) {
    	$rand = mt_rand(100000, 999999);
      $imageName = time(). $rand .'.'.$request->image->extension();
      $request->image->move(public_path('images/product'), $imageName);
      $product->image= $imageName;
    }
    
    if ($product->save()) {
        if ($request->input('alternative')) {

            $collection = AlternativeProduct::where('product_id', $request->id)->get(['id']);
            AlternativeProduct::destroy($collection->toArray());


          foreach($request->input('alternative') as $key => $value)
          {
              $alt = new AlternativeProduct();
              $alt->product_id = $product->id;
              $alt->alternative_product_id = $value;
              $alt->created_by = Auth::user()->id;
              $alt->save();
          }
        }

        if ($request->replacement) {

            $rplmnt = Replacement::where('product_id', $request->id)->get(['id']);
            Replacement::destroy($rplmnt->toArray());

            $allreplacementid = explode(',',$request->replacement);

            foreach($allreplacementid as $key => $value)
            {
                $replace = new Replacement();
                $replace->product_id = $product->id;
                $replace->replacementid = $value;
                $replace->created_by = Auth::user()->id;
                $replace->save();
            }
        }
    }

      return $product;
    }

    public function getproduct(Request $request)
    {
        $productDtl = Product::where('id', '=', $request->product)->first();
        $chkstock = Stock::where('product_id',$request->product)->where('branch_id','=', Auth::user()->branch_id)->first();

        $stocks = DB::table('stocks')
                ->join('products', 'stocks.product_id', '=', 'products.id')
                ->join('branches', 'stocks.branch_id', '=', 'branches.id')
                ->select('stocks.*', 'products.productname', 'branches.name as branchname')
                ->where('stocks.product_id',$request->product)
                ->where('stocks.branch_id','!=', Auth::user()->branch_id)
                ->where('quantity','>', 0)
                ->get();

        if(empty($productDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            if (empty($chkstock)) {
                return response()->json(['status'=> 300,'productname'=>$productDtl->productname,'product_id'=>$productDtl->id, 'sellingprice'=>$productDtl->selling_price,'selling_price_with_vat'=>$productDtl->selling_price_with_vat, 'part_no'=>$productDtl->part_no, 'vat_percent'=>$productDtl->vat_percent, 'vat_amount'=>$productDtl->vat_amount,'stocks'=>$stocks,'chkstock'=>'0']);
            } else {
                return response()->json(['status'=> 300,'productname'=>$productDtl->productname,'product_id'=>$productDtl->id, 'sellingprice'=>$productDtl->selling_price,'selling_price_with_vat'=>$productDtl->selling_price_with_vat, 'part_no'=>$productDtl->part_no, 'vat_percent'=>$productDtl->vat_percent, 'vat_amount'=>$productDtl->vat_amount,'stocks'=>$stocks,'chkstock'=>$chkstock->quantity]);
            }
            
            
        }

    }

    public function getAllProduct(){
        $product = Product::where('branch_id', Auth::user()->branch_id)->get();
        return $product;
    }

}
