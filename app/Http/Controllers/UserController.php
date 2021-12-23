<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    public function show($id)
    {
        // if (auth()->check()) {
        $user = User::findOrFail($id);

        return response()->json([
            "body" => $user,
            'status' => 1
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // // if (auth()->check()) {
        if (isset($_GET['$skip'])) {
            $users = User::orderBy('created_at', 'desc')
                ->where('status', '>', -1)
            ;

            $sqlCount = clone $users;
            
            $sqlCount = $users->get();

            $count = count($sqlCount);

            $users = $users
                ->skip($_GET['$skip'])
                ->take($_GET['$top'])
                ->get();

        } else {
            $users = User::where('status', '>', -1)->get();
            $count = count($users);
        }
        return response()->json([
            "body" => [
                "data" => $users,
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'string|max:20',
            'occupation' => 'string',
            'company' => 'string',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        // var_dump($validator->errors());
        if ($validator->fails()) {
            return response()->json([
                "body" => [
                    'validacion' => $validator->errors()->toJson()
                ],
                "message" => current((array) $validator->errors()),
                'status' => 0
            ], 400);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            [
                'password' => bcrypt($request->password),
                'status' => 2
            ]
        ));
        
        $imageBim = $request->file('imageBim') ? $request->file('imageBim') : null;

        $allowedfileExtension = ['jpg', 'png', 'jpeg'];

        if($imageBim) {

            $extension = strtolower($imageBim->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtension);
            
            if (!$check) {
                $error[] = 'Formato de imagen inválida';
            }
            else
            {
                $path = $imageBim->store('/public/images/users');
                $pathPublic  = Storage::url($path);
                //store image file into directory and db
                User::where('id', $user->id)
                    ->update(['image' => $pathPublic ? $pathPublic : '']);
            }
        }

        // if (auth()->check()) {
        return response()->json([
            "body" => [
                'message' => '¡Usuario registrado exitosamente!',
                'user' => $user
            ],
            'status' => 1
        ], 201);
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

        $existUser = User::findOrFail($request->id);
        
        if(!$existUser)
        {
            return response()->json([
                "msg" => 'No se encontro al usuario',
                'status' => 0
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'string|max:20',
            'occupation' => 'string',
            'company' => 'string',
            'email' => 'string|email|max:100|unique:users,email,'.$existUser->id,
            'password' => 'string|min:6|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "body" => [
                    'validacion' => $validator->errors()->toJson()
                ],
                'status' => 0
            ], 400);
        }

        $user = User::where('id', $request->id)
            ->update(array_merge(
                $validator->validate(),
                [
                    'password' => $request->password ? bcrypt($request->password) : $existUser->password,
                    'status' => 2,
                    'id' => $existUser->id
                ]
            ));
        
        $imageBim = $request->file('imageBim') ? $request->file('imageBim') : null;

        $allowedfileExtension = ['jpg', 'png', 'jpeg'];

        if($imageBim) {

            $extension = strtolower($imageBim->getClientOriginalExtension());

            $check = in_array($extension, $allowedfileExtension);
            
            if (!$check) {
                $error[] = 'Formato de imagen inválida';
            }
            else
            {
                $path = $imageBim->store('/public/images/users');
                $pathPublic  = Storage::url($path);
                //store image file into directory and db
                User::where('id', $request->id)
                    ->update(['image' => $pathPublic ? $pathPublic : '']);
            }
        }

        // if (auth()->check()) {
        return response()->json([
            "body" => [
                'message' => '¡Usuario editado exitosamente!',
                'user' => $user
            ],
            'status' => 1
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       
        $user = User::findOrFail($request->id);
        
        $user->status = -1;
        
        // if (auth()->check()) {
         
        if ($user->delete()) {
            return response()->json([
                "body" => $user,
                'status' => 1
            ]);
        } else {
            return response()->json([
                "msg" => 'No se pudo eliminar el usuario.',
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
