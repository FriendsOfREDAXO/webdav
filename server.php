<?php

use Sabre\DAV;

unset($REX);
$REX['REDAXO'] = true;
$REX['HTDOCS_PATH'] = '../../../../';
$REX['BACKEND_FOLDER'] = 'redaxo';

require '../../../src/core/boot.php';
require_once 'vendor/autoload.php';

$publicDir = new MediapoolRoot();
$server = new DAV\Server($publicDir);

$plugin = new \Sabre\DAV\Browser\Plugin();
$server->addPlugin($plugin);

// We're required to set the base uri, it is recommended to put your webdav server on a root of a domain
// XXX this assumes a GET URL of http://localhost/redaxo/redaxo/src/addons/webdav/server.php/
$server->setBaseUri('/redaxo/redaxo/src/addons/webdav/server.php');

// And off we go!
$server->exec();