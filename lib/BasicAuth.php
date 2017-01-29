<?php

use Sabre\DAV;

class BasicAuth extends DAV\Auth\Backend\AbstractBasic {
    function validateUserPass($username, $password)
    {
        $login = new rex_backend_login();
        $login->setLogin($username, $password);
        if ($login->checkLogin()) {
            $user = $login->getUser();
            // XXX the mediapool check seems not to work right now.
            return $user->isAdmin() || $user->getComplexPerm('media') && $user->getComplexPerm('media')->hasCategoryPerm(0);
        }
        return false;
    }
}