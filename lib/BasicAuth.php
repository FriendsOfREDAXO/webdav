<?php

use Sabre\DAV;

class BasicAuth extends DAV\Auth\Backend\AbstractBasic {
    function validateUserPass($username, $password)
    {
        $login = new rex_backend_login();
        $login->setLogin($username, $password);
        rex_logger::factory()->info("user:$username,pw:$password");
        if ($login->checkLogin()) {
            $user = $login->getUser();
            return $user->isAdmin();
        }
        return false;
    }
}