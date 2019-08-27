<?php
use Tests\BaseConfig;
use App\Models\User;
use App\Models\Recipe;

class RecipesControllerTest extends BaseConfig
{
    private $http;
    private $recipeId;

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
        $this->http = null;
        $this->recipeId = null;
    }

    public function testCreateRecipesIfNotLoggedIn()
    {
        $payload = [];
        $payload['json'] = $this->getRecipePayload();

        try {
            $response = $this->http->post('/recipes', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(403, $response->getStatusCode());

            $contentType = $response->getHeaders()["Content-Type"][0];
            $this->assertEquals("application/json", $contentType);

            $body = json_decode($response->getBody());
            $this->assertEquals("Authorization failed", $body->{"error"});
        }
    }

    public function testCreateRecipesFieldsValidation()
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
        $payload['json'] = [];
        $payload['headers'] = ['Authorization' => $body->{"accessToken"}];

        try {
            $response = $this->http->post('/recipes', $payload);
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());

            $contentType = $response->getHeaders()["Content-Type"][0];
            $this->assertEquals("application/json", $contentType);

            $body = json_decode($response->getBody());
            $this->assertEquals("The name field is required.", $body->{"name"}[0]);
            $this->assertEquals("The prep time field is required.", $body->{"prepTime"}[0]);
            $this->assertEquals("The difficulty field is required.", $body->{"difficulty"}[0]);
            $this->assertEquals("The vegetarian field is required.", $body->{"vegetarian"}[0]);
        }
    }

    public function testRecipeCrud()
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

        $response = $this->http->get('/recipes');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $body = json_decode($response->getBody());

        $totalRecords = $body->{"totalRecords"};
        $this->assertInternalType('int', $totalRecords);

        $page = $body->{"page"};
        $this->assertInternalType('int', $page);

        $limit = $body->{"limit"};
        $this->assertInternalType('int', $limit);

        $data = $body->{"data"};
        $this->assertTrue(is_array($data));

        // read
        $response = $this->http->get('/recipes/'.$this->recipeId);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        // update
        $payload['json']['name'] = 'updated reciped name';
        $response = $this->http->put('/recipes/'.$this->recipeId, $payload);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        // delete
        $response = $this->http->delete('/recipes/'.$this->recipeId, $payload);
        $this->assertEquals(200, $response->getStatusCode());
        $this->recipeId = null;
    }

    public function testInvalidRecipeId()
    {
        try {
            $response = $this->http->get('/recipes/1256982');
        } catch (Exception $e) {
            $response = $e->getResponse();
            $this->assertEquals(404, $response->getStatusCode());

            $contentType = $response->getHeaders()["Content-Type"][0];
            $this->assertEquals("application/json", $contentType);
        }
    }
}
