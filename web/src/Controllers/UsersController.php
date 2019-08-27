<?php
namespace App\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Libs\Validation;
use App\Libs\AccessToken;

class UsersController
{
    /**
     * @param  Request http request object
     * @return object $user
     */
    public function create(Request $request)
    {        
        $errors = $this->validateRequest($request);
        if ($errors) return response($errors, 400);

        $user = new User;
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->email = $request->email;
        $user->password = password_hash($request->password, PASSWORD_DEFAULT);
        $user->save();

        unset($user->password);

        return $user;
    }

    /**
     * @param  Request http request object
     * @return object $user|$error
     */
    public function login(Request $request)
    {        
        $validation = Validation::getInstance();

        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $errors = null;

        $validator = $validation->make($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
        }

        if ($errors) return response($errors, 400);

        $user = User::where('email', $request->email)->first();

        if ($user && password_verify($request->password, $user->password)) {
            return ['accessToken' => AccessToken::generate($user->id)];
        }

        return response(['error' => 'Incorrect username or password'], 401);
    }

    /**
     * @param  Request http request object
     * @return array $errors
     */
    private function validateRequest($request)
    {
        $validation = Validation::getInstance();

        $data = [
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'password' => $request->password,
        ];

        $rules = [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];

        $errors = null;

        $validator = $validation->make($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
        }

        return $errors;
    }
}
