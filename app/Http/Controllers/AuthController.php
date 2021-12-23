<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'update']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        
        if (!$token = auth('customer')->attempt($credentials)) {
            return response()->json(
                [
                    'msg' => 'error',
                    'status' => 0
                ],
                401
            );
        }

        return $this->respondWithToken($token, auth()->user());

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(
            [
                'msg' => 'Cerrar sesión correctamente',
                'status' => 1
            ],
            401
        );
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $credentials)
    {
        return response()->json([
            "body" => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $credentials,
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
            'status' => 1
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'string|max:20',
            'occupation' => 'required|string',
            'company' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "body" => [
                    'validacion' => $validator->errors()->toJson()
                ],
                'status' => 0
            ], 400);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            "body" => [
                'message' => '¡Usuario registrado exitosamente!',
                'user' => $user
            ],
            'status' => 1
        ], 201);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->company = $request->company;
        $user->occupation = $request->occupation;

        // if (auth()->check()) {
            // if ($user->save()) {
                return response()->json([
                    "msg" => 'Editado correctamente',
                    "body" => [
                        $user
                    ],
                    'status' => 1
                ]);
            // } else {
            //     return response()->json([
            //         "msg" => 'No se pudo cambiar la categoria.',
            //         'status' => 0
            //     ]);
            // }
        // } else {
        //     return response()->json([
        //         "msg" => 'Necesita iniciar sesión.',
        //         'status' => 0
        //     ]);
        // }
    }
}
