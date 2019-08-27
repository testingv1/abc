<?php
namespace App\Libs;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Factory;

class Validation
{
    public static function getInstance()
    {
        global $capsule;

        $loader = new FileLoader(new Filesystem, 'src/lang');
        $translator = new Translator($loader, 'en');
        $presence = new DatabasePresenceVerifier($capsule->getDatabaseManager());

        $validation = new Factory($translator, new Container);
        $validation->setPresenceVerifier($presence);

        return $validation;
    }
}
