<?php

use Sabre\DAV;

unset($REX);
$REX['REDAXO'] = true;
$REX['HTDOCS_PATH'] = '../../../../';
$REX['BACKEND_FOLDER'] = 'redaxo';

require '../../../src/core/boot.php';
require_once 'vendor/autoload.php';

if (!((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443)) {
    throw new Exception('webdav requires a https/secure connection');
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