<?php
use Tests\BaseConfig;
use App\Models\User;

class UsersControllerTest extends BaseConfig
{
    private $http;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/users']);
    }

    public function tearDown()
    {
        User::where('email', $this->testUserEmail)->delete();
        $this->http = null;
    }

    public function testUserLoginFailure()
    {
        $payload = [];
        $payload['json'] = $this->getUserSignupPayload();

        try {
            $response = $this->http->post('/users/login', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(401, $response->getStatusCode());

            $body = json_decode($response->getBody());
            $this->assertEquals("Incorrect username or password", $body->{"error"});
        }
    }

    public function testUserLoginSuccess()
    {
        $payload = [];
        $payload['json'] = $this->getUserSignupPayload();

        $response = $this->http->post('', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $payload = [];
        $payload['json'] = $this->getUserLoginPayload();

        $response = $this->http->post('/users/login', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody());
        $this->assertTrue(is_string($body->{"accessToken"}));
    }

    public function testUserSignupSuccessAndExistingEmailValidation()
    {
        $payload = [];
        $payload['json'] = $this->getUserSignupPayload();

        $response = $this->http->post('', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        try {
            $response = $this->http->post('', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());

            $body = json_decode($response->getBody());
            $this->assertEquals("The email has already been taken.", $body->{"email"}[0]);
        }
    }

    public function testUserSignupFailureIfFieldsMissing()
    {
        $payload = [];
        $payload['json'] = [];

        try {
            $response = $this->http->post('', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());

            $body = json_decode($response->getBody());
            $this->assertEquals("The email field is required.", $body->{"email"}[0]);
            $this->assertEquals("The first name field is required.", $body->{"firstName"}[0]);
            $this->assertEquals("The last name field is required.", $body->{"lastName"}[0]);
            $this->assertEquals("The password field is required.", $body->{"password"}[0]);
        }
    }

    public function testUserSignupPasswordLength()
    {
        $payload = [];
        $payload['json'] = ['password' => 'weak'];

        try {
            $response = $this->http->post('', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());

            $body = json_decode($response->getBody());
            $this->assertEquals("The password must be at least 6 characters.", $body->{"password"}[0]);
        }
    }
}
