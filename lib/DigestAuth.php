<?php

use Sabre\DAV;

class DigestAuth extends DAV\Auth\Backend\AbstractDigest {
    function getDigestHash($realm, $username) {
        $login = rex_sql::factory();
        $login->setTable(rex::getTablePrefix() . 'user');
        $login->setWhere('login = :login AND status = 1 AND login_tries < 10', array(':login' => $username));
        $login->execute();

        if ($login->getRows() == 1 && $login->getValue('admin') == 1) {
            // XXX somehow generate a password/digest per user
            rex_logger::factory()->info($username .':'. $this->realm . ':'. md5($username .':'. $this->realm .':'. $password));
            $password = 'test';
            return $username .':'. $this->realm . ':'. md5($username .':'. $this->realm .':'. $password);
        }
        return null;
    }
}