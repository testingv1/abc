<?php
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

/**
 * @param  array $data
 * @param  integer $statusCode
 * @return json response
 */
function response($data = [], $statusCode = 200)
{
    return Response::create($data, $statusCode, ['Content-Type' => 'application/json']);
}

function authUserId()
{
    $token = Request::capture()->header('Authorization');
    $payload = JWT::decode($token, JWT_SECRET, ['HS256']);
    return $payload->userId;
}
