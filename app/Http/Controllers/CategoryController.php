<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use JWTAuth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // // if (auth()->check()) {

        $categories = Category::orderBy('created_at', 'desc')
            ->where('is_active', 1)
        ;

        if(isset($_GET['level']) && $_GET['level'] > 0) {
            $categories->where('level', $_GET['level']);
        } else {
            $categories->where('level', null);
        }

        if(isset($_GET['parent'])) {
            $categories->where('parent_id', $_GET['parent']);
        }

        $sqlCount = clone $categories;
        $sqlCount = $categories->get();

        $count = count($sqlCount);

        if (isset($_GET['$skip'])) {

            $categories
                ->skip($_GET['$skip'])
                ->take($_GET['$top'])
                ;
        }

        $categories = $categories->get();

        foreach ($categories as $category) {
            $category->section;
        }

        return response()->json([
            "body" => [
                "data" => $categories,
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
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::of($request->name)->slug('-')->trim("-");
        $category->icon = $request->icon ? $request->icon : '';
        $category->is_active = 1;
        $category->section_id = $request->section_id;
        $category->level = $request->level ? $request->level : null;
        $category->parent_id = $request->parent_id ? $request->parent_id : null;

        // if (auth()->check()) {
        if ($category->save()) {
            return response()->json([
                "msg" => 'Agregado satisfactoriamente',
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo agregar la categoria.',
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
        $categories = Category::findOrFail($id);
        return response()->json([
            "body" => $categories,
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
        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = Str::of($request->name)->slug('-')->trim("-");
        $category->icon = $request->icon ? $request->icon : '';
        $category->section_id = $request->section_id;

        // if (auth()->check()) {
        if ($category->save()) {
            return response()->json([
                "msg" => 'Editado correctamente',
                "body" => [
                    $category
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
        $category = Category::findOrFail($request->id);
        $category->is_active = 0;

        $category->save();

        // if (auth()->check()) {
        if ($category->save()) {
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
