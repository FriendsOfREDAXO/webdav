<?php

use Sabre\DAV;

require_once 'vendor/autoload.php';

if (!((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443)) {
    throw new Exception('webdav requires a https/secure connection');
}

$user = rex_backend_login::createUser();
if ($user) {
    // we need the mediapool api, which will only be loaded in mediapool/boot.php
    // when a backend user is logged in
    include_once rex_path::core('packages.php');
}

$publicDir = new MediapoolRoot();
$server = new DAV\Server($publicDir);

$plugin = new DAV\Browser\Plugin();
$server->addPlugin($plugin);

$authPlugin = new DAV\Auth\Plugin();
$authPlugin->addBackend(new BasicAuth());
$server->addPlugin($authPlugin);

$lockBackend = new DAV\Locks\Backend\File(rex_path::addonData('webdav', 'locks.dat'));
$lockPlugin = new Sabre\DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// We're required to set the base uri, it is recommended to put your webdav server on a root of a domain
$server->setBaseUri($_SERVER['SCRIPT_NAME']);

// And off we go!
$server->exec();