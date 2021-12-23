<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if (!$request->hasFile('fileName')) {
            return response()->json([
                "msg" => 'Error al cargar imagen',
                'status' => 0
            ], 400);
        }

        $allowedfileExtension = ['pdf', 'jpg', 'png'];
        $files = $request->file('fileName');
        
        if(!is_array($files))
        {
            $files = [$files];    
        }

        $error = [];

        foreach ($files as $file) {

            $extension = strtolower($file->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtension);
            
            if ($check) {
                // foreach ($files as $mediaFiles) {

                    $path = $file->store('public/images');
                    $name = $file->getClientOriginalName();

                    //store image file into directory and db
                    $save = new Gallery();
                    $save->name = $name;
                    $save->path = $path;
                    $save->type = 1;
                    $save->id_product = $request->id_product;
                    $save->is_active = 1;
                    $save->save();
                    
                // }
            } else {
                $error[] = 'Formato de imagen inválida';
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
        else
        {
            return response()->json([
                "msg" => 'Imagen subida correctamente.',
                'status' => 1
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
