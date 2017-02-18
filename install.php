<?php

// create a lock-store
rex_file::put(rex_path::addonData('webdav', 'locks.dat'), '');

$frontController = <<<'EOD'
<?php

unset($REX);
$REX['REDAXO'] = true;
$REX['HTDOCS_PATH'] = '../';
$REX['BACKEND_FOLDER'] = 'redaxo';
$REX['LOAD_PAGE'] = false;

require 'src/core/boot.php';

require_once "./src/addons/webdav/server.php";
EOD;

// create a front-controller
rex_file::put(rex_path::backend('webdav.php'), $frontController);
