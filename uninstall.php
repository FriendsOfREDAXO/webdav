<?php

rex_file::delete(rex_path::addonData('webdav', 'locks.dat'));
rex_file::delete(rex_path::backend('webdav.php'));
