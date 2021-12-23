<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (auth()->check()) {
        if (isset($_GET['$skip'])) {
            
            $product = Product::orderBy('created_at', 'desc')
                ->where('is_active', 1);
            
            $sqlCount = clone $product;
        
            $sqlCount = $product->get();

            $count = count($sqlCount);
            
            $product = $product->skip($_GET['$skip'])
                ->take($_GET['$top'])
                ->get();
        } else {
            $product = Product::all();
            $count = count($product);
        }

        foreach ($product as $item) {
            $item->categories;
            $item->brands;

            $item->images = Gallery::where('id_product', $item->id)->where('type', 1)->get();
            $item->files = Gallery::where('id_product', $item->id)->where('type', 2)->get();
        }

        return response()->json([
            "body" => [
                "data" => $product,
                "count" => $count
            ],
            'status' => 1
        ]);
        // } else {
        //     return response()->json([
        //         "msg" => 'Necesita iniciar sesión.',
        //         'status' => 0
        //     ]);
        // }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description ? $request->description : '';
        $product->brand_id = $request->brand_id;
        $product->category_id = $request->category_id;
        $product->user_id = auth()->id();
        $product->technical_specifications = $request->technical_specifications ? $request->technical_specifications : '';
        $product->links = $request->links ? $request->links : '';
        $product->related_information = $request->related_information ? $request->related_information : '';
        $product->classification = $request->classification ? $request->classification : '';
        $product->properties = $request->properties ? $request->properties : '';
        $product->subcategory_id = $request->subcategory_id  ? $request->subcategory_id  : null;
        $product->is_active = 1;
        $product->is_popular = 1;
        // $product->user_id = 1;
        
        $imagesBim = $request->file('imagesBim') ? $request->file('imagesBim') : [];
        $filesBim = $request->file('filesBim') ? $request->file('filesBim') : [];
        
        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
        $error = [];

        foreach ($imagesBim as $file) {

            $extension = strtolower($file->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtension);
            
            if (!$check) {
                $error[] = 'Formato de imagen inválida';
            }
        }

        $allowedfileExtensionFile = ['pdf', 'rtv', 'doc', 'csv', 'docx', 'xls', 'xlsx', 'rfa','rvt','rar', 'zip', 'ifc'];
        foreach ($filesBim as $file) {

            $extension = strtolower($file->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtensionFile);
            
            if (!$check) {
                $error[] = 'Formato de archivo inválida';
            }
        }

        if(count($error) > 0)
        {
            return response()->json([
                "msg" => current($error),
                'error' => $error, 
                'status' => 0
            ], 422);
        }

        // if (auth()->check()) {
        if (Category::findOrFail($request->category_id)) {
            
                if ($product->save()) {
                    $idProducto = $product->id;

                    foreach($imagesBim as $file) {

                        $extension = strtolower($file->getClientOriginalExtension());
                        $path = $file->store('/public/images/products');
                        $name = $file->getClientOriginalName();
                        $pathPublic  = Storage::url($path);
                        //store image file into directory and db
                        $save = new Gallery();
                        $save->name = $name;
                        $save->path = $pathPublic;
                        $save->type = 1;
                        $save->id_product = $idProducto;
                        $save->is_active = 1;
                        $save->save();
                    }

                    foreach($filesBim as $file) {

                        $extension = strtolower($file->getClientOriginalExtension());
                        
                        $path = $file->store('/public/files/products');
                        $name = $file->getClientOriginalName();
                        $pathPublic  = Storage::url($path);
                        //store image file into directory and db
                        $save = new Gallery();
                        $save->name = $name;
                        $save->path = $pathPublic;
                        $save->type = 2;
                        $save->id_product = $idProducto;
                        $save->is_active = 1;
                        $save->save();
                    }

                    return response()->json([
                        "body" => $product,
                        'status' => 1
                    ]);
                } else {
                    return response()->json([
                        "msg" => 'No se pudo agregar el producto.',
                        'status' => 0
                    ]);
                }
           
        } else {
            return response()->json([
                "msg" => 'No se pudo agregar el producto porque la categoria no existe.',
                'status' => 0
            ]);
        }
        // } else {
        //     return response()->json([
        //         "msg" => 'Necesita iniciar sesión.',
        //         'status' => 0
        //     ]);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // if (auth()->check()) {
        $product = Product::findOrFail($id);
        $product->categories;
        $product->brands;

        if($product)
        {
            $product->images = Gallery::where('id_product', $id)->where('type', 1)->get();
            $product->files = Gallery::where('id_product', $id)->where('type', 2)->get();
        }
        
            $product->category_id = (int) $product->category_id;
            $product->brand_id = (int) $product->brand_id;
        

        return response()->json([
            "body" => $product,
            'status' => 1
        ]);
        // } else {
        //     return response()->json([
        //         "msg" => 'Necesita iniciar sesión.',
        //         'status' => 0
        //     ]);
        // }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $product->name = $request->name;
        $product->description = $request->description ? $request->description : '';
        $product->brand_id = $request->brand_id;
        $product->category_id = $request->category_id;
        $product->technical_specifications = $request->technical_specifications ? $request->technical_specifications : '';
        $product->links = $request->links ? $request->links : '';
        $product->related_information = $request->related_information ? $request->related_information : '';
        $product->classification = $request->classification ? $request->classification : '';
        $product->properties = $request->properties ? $request->properties : '';
        $product->subcategory_id = $request->subcategory_id  ? $request->subcategory_id  : null;
        $product->is_active = 1;

        $images = $request->images ? json_decode($request->images) : [];
        $files = $request->filesProduct ? json_decode($request->filesProduct) : [] ;

        $imagesProduct = Gallery::where('id_product', $request->id)->where('type', 1)->get()->toArray();
        $filesProduct = Gallery::where('id_product', $request->id)->where('type', 2)->get()->toArray();

        $imagesBim = $request->file('imagesBim') ? $request->file('imagesBim') : [];
        $filesBim = $request->file('filesBim') ? $request->file('filesBim') : [];
        
        $arIdImagesProducts = array_column($images, 'id');
        $arIdFilesProducts = array_column($files, 'id');

        $arImagesDelete = [];
        $arFilesDelete = [];
        
        foreach ($imagesProduct as $image) {
            
            if(!in_array($image['id'], $arIdImagesProducts)) {
                $arImagesDelete[] = $image;
            }
        }
        
        foreach ($filesProduct as $file) {
            
            if(!in_array($file['id'], $arIdFilesProducts)) {
                $arFilesDelete[] = $file;
            }
        }

        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
        $error = [];

        foreach ($imagesBim as $file) {

            $extension = strtolower($file->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtension);
            
            if (!$check) {
                $error[] = 'Formato de imagen inválida';
            }
        }

        $allowedfileExtensionFile = ['pdf', 'rtv', 'doc', 'csv', 'docx', 'xls', 'xlsx', 'rfa', 'rvt', 'rar', 'zip', 'ifc'];
        foreach ($filesBim as $file) {

            $extension = strtolower($file->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtensionFile);
            
            if (!$check) {
                $error[] = 'Formato de archivo inválida';
            }
        }

        if(count($error) > 0)
        {
            return response()->json([
                "msg" => current($error),
                'error' => $error, 
                'status' => 0
            ], 422);
        }

        
        // if (auth()->check()) {
        if (Category::findOrFail($request->category_id)) {
            // if (Brand::findOrFail($request->brand_id)) {
                if ($product->save()) {

                    $idProducto = $product->id;
                    
                    foreach ($arImagesDelete as $file) {
                        Gallery::where('id',$file['id'])->delete();
                    }

                    foreach ($arFilesDelete as $file) {
                        Gallery::where('id',$file['id'])->delete();
                    }


                    foreach($imagesBim as $file) {

                        $extension = strtolower($file->getClientOriginalExtension());
                        $path = $file->store('/public/images/products');
                        $name = $file->getClientOriginalName();
                        $pathPublic  = Storage::url($path);
                        //store image file into directory and db
                        $save = new Gallery();
                        $save->name = $name;
                        $save->path = $pathPublic;
                        $save->type = 1;
                        $save->id_product = $idProducto;
                        $save->is_active = 1;
                        $save->save();
                    }

                    foreach($filesBim as $file) {

                        $extension = strtolower($file->getClientOriginalExtension());
                        
                        $path = $file->store('/public/files/products');
                        $name = $file->getClientOriginalName();
                        $pathPublic  = Storage::url($path);
                        //store image file into directory and db
                        $save = new Gallery();
                        $save->name = $name;
                        $save->path = $pathPublic;
                        $save->type = 2;
                        $save->id_product = $idProducto;
                        $save->is_active = 1;
                        $save->save();
                    }

                    return response()->json([
                        "body" => $product,
                        'status' => 1
                    ]);
                } else {
                    return response()->json([
                        "msg" => 'No se pudo actualizar el producto.',
                        'status' => 0
                    ]);
                }
            // } else {
            //     return response()->json([
            //         "msg" => 'No se pudo actualizar el producto porque la marca no existe.',
            //         'status' => 0
            //     ]);
            // }
        } else {
            return response()->json([
                "msg" => 'No se pudo actualizar el producto porque la categoria no existe.',
                'status' => 0
            ]);
        }
        // } else {
        //     return response()->json([
        //         "msg" => 'Necesita iniciar sesión.',
        //         'status' => 0
        //     ]);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       
        $product = Product::findOrFail($request->id);
        
        $product->is_active = 0;
        
        // if (auth()->check()) {
         
        if ($product->delete()) {
            return response()->json([
                "body" => $product,
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo actualizar el producto.',
                'status' => 0
            ]);
        }
        // } else {
        //     return response()->json([
        //         "msg" => 'Necesita iniciar sesión.',
        //         'status' => 0
        //     ]);
        // }
    }
}
