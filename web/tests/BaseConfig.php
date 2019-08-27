<?php
namespace Tests;

require_once 'src/config/app.php';
use Illuminate\Database\Capsule\Manager as Capsule;

class BaseConfig extends \PHPUnit\Framework\TestCase
{
    public $schema;
    public $capsule;
    public $testUserEmail;

    public function __construct()
    {
        parent::__construct();

        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => DB_DRIVER,
            'host'      => DB_HOST,
            'port'      => DB_PORT,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => DB_CHARSET,
            'collation' => DB_COLLATION,
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();

        $this->testUserEmail = 'test@gmail.com';
    }

    public function testSetup()
    {
        $this->assertTrue(true);
    }

    public function getUserSignupPayload()
    {
        return [
            'email' => $this->testUserEmail,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'password' => 'testpassword'
        ];
    }

    public function getUserLoginPayload()
    {
        return [
            'email' => $this->testUserEmail,
            'password' => 'testpassword'
        ];
    }

    public function getRecipePayload()
    {
        return [
            'name' => 'hello',
            'prepTime' => '2hrs',
            'difficulty' => 1,
            'vegetarian' => true
        ];
    }

    public function getRatingPayload()
    {
        return ['rating' => 2];
    }

    public function getRatingErrorPayload()
    {
        return ['rating' => 7];
    }
}
