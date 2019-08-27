<?php
use Tests\BaseConfig;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Rating;

class RatingsControllerTest extends BaseConfig
{
    private $http;
    private $recipeId;
    private $ratingId;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost']);
    }

    public function tearDown()
    {
        User::where('email', $this->testUserEmail)->delete();
        if ($this->recipeId) {
            Recipe::where('id', $this->recipeId)->delete();
        }
        if ($this->ratingId) {
            Rating::where('id', $this->ratingId)->delete();
        }
        $this->http = null;
        $this->recipeId = null;
        $this->ratingId = null;
    }

    public function testRecipeRating()
    {
        $payload = [];
        $payload['json'] = $this->getUserSignupPayload();

        $response = $this->http->post('/users', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $payload = [];
        $payload['json'] = $this->getUserLoginPayload();

        $response = $this->http->post('/users/login', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody());
        $this->assertTrue(is_string($body->{"accessToken"}));

        $payload = [];
        $payload['json'] = $this->getRecipePayload();
        $payload['headers'] = ['Authorization' => $body->{"accessToken"}];

        // create
        $response = $this->http->post('/recipes', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $body = json_decode($response->getBody());
        $this->recipeId = $body->id;

        // rate
        $payload['json'] = $this->getRatingPayload();
        $response = $this->http->post('/recipes/'.$this->recipeId.'/rating', $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody());
        $this->ratingId = $body->id;

        // check rating validations
        $payload['json'] = $this->getRatingErrorPayload();
        try {
            $response = $this->http->post('/recipes/'.$this->recipeId.'/rating', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());

            $contentType = $response->getHeaders()["Content-Type"][0];
            $this->assertEquals("application/json", $contentType);
        }
    }
}
