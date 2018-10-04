<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('/users', function (Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:190|unique:users',
            'password' => 'required|min:8|max:20|confirmed'
        ]);
        $data = $request->all();
        $data['password'] = \Hash::make($data['password']);
        $model = \App\User::create($data);
        return response()->json($model, 201);
    });

    $router->post('/login', function (Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $email = $request->get('email');
        $password = $request->get('password');
        $user = \App\User::where('email', '=', $email)->first();
        if (!$user || !\Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Invalid credentilas'], 400);
        } else {
            return response()->json(['message' => 'Congratulations'], 200);
        }
    });
});