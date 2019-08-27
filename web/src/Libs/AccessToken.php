<?php
namespace App\Libs;

use Firebase\JWT\JWT;

class AccessToken
{
    /**
     * @param  int $userId
     * @return string accessToken
     */
    public static function generate($userId)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 60*60*24*7;
        $payload = array(
            'userId' => $userId,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );

        return JWT::encode($payload, JWT_SECRET, 'HS256');
    }

    /**
     * @param  string $token
     * @return object accessToken
     */
    public static function decode($token)
    {
        return JWT::decode($token, JWT_SECRET, ['HS256']);
    }
}
