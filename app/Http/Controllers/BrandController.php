<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;

use JWTAuth;

class BrandController extends Controller
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
            $brands = Brand::orderBy('created_at', 'desc')
                ->where('is_active', 1)
            ;

            $sqlCount = clone $brands;
            $sqlCount = $brands->get();
            $count = count($sqlCount);

            $brands = $brands
                ->skip($_GET['$skip'])
                ->take($_GET['$top'])
                ->get()
            ;

        } else {
            $brands = Brand::where('is_active', 1)->get();
            $count = count($brands);
        }
        return response()->json([
            "body" => [
                "data" => $brands,
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
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::of($request->name)->slug('-')->trim("-");
        $brand->is_active = 1;


        // if (auth()->check()) {
        if ($brand->save()) {
            return response()->json([
                "msg" => 'Agregado satisfactoriamente',
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo agregar la marca.',
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
        $brand = Brand::findOrFail($id);
        return response()->json([
            "body" => $brand,
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
        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::of($request->name)->slug('-')->trim("-");

        $brand->save();

        // if (auth()->check()) {
        if ($brand->save()) {
            return response()->json([
                "msg" => 'Editado correctamente',
                "body" => [
                    $brand
                ],
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo cambiar la categoria.',
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
        $brand = Brand::findOrFail($request->id);
        $brand->is_active = 0;

        $brand->save();

        // if (auth()->check()) {
        if ($brand->save()) {
            return response()->json([
                "msg" => 'Categoria eliminada correctamente',
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo eliminar',
                'status' => 1
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
