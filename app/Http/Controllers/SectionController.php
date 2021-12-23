<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // // if (auth()->check()) {
        if (isset($_GET['$skip'])) {
            $sections = Section::orderBy('created_at', 'desc')
                ->where('state', '>', -1);
        
            $sqlCount = clone $sections;
            $sqlCount = $sections->get();
            $count = count($sqlCount);

            $sections = $sections
                ->skip($_GET['$skip'])
                ->take($_GET['$top'])
                ->get()
                ;
        } else {
            $sections = Section::where('state', '>', -1)->get();
            $count = count($sections);
        }
        return response()->json([
            "body" => [
                "data" => $sections,
                "count" => $count
            ],
            'status' => 1
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $section = new Section();
        $section->name = $request->name;
        $section->slug = Str::of($request->name)->slug('-')->trim("-");
        $section->state = 2;
        // if (auth()->check()) {
        if ($section->save()) {
            return response()->json([
                "msg" => 'Agregado satisfactoriamente',
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo agregar la sección.',
                'status' => 0
            ]);
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
        // if (auth()->check()) {
        $sections = Section::findOrFail($id);
        return response()->json([
            "body" => $sections,
            'status' => 1
        ]);
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
        $section = Section::findOrFail($request->id);
        $section->name = $request->name;
        $section->slug = Str::of($request->name)->slug('-')->trim("-");

        // if (auth()->check()) {
        if ($section->save()) {
            return response()->json([
                "msg" => 'Editado correctamente',
                "body" => [
                    $section
                ],
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo cambiar la categoria.',
                'status' => 0
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $section = Section::findOrFail($request->id);
        $section->state = -1;
        $section->save();
        // if (auth()->check()) {
        if ($section->save()) {
            return response()->json([
                "msg" => 'Sección eliminada correctamente',
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo eliminar',
                'status' => 1
            ]);
        }
    }
}
