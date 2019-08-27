<?php
use Tests\BaseConfig;

class APIStatusTest extends BaseConfig
{
    private $http;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/']);
    }

    public function tearDown()
    {
        $this->http = null;
    }

    public function testGet()
    {
        $response = $this->http->request('GET');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $status = json_decode($response->getBody())->{"status"};
        $this->assertRegexp('/running/', $status);
    }
}
