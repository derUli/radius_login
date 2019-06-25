<?php

use Dapphp\Radius\Radius;

class RadiusAuthenticator {

    private $mainClass;
    private $cfg;
    private $error = null;

    public function __construct($mainClass) {
        $this->mainClass = $mainClass;
        $this->cfg = $mainClass->getConfiguration();
    }

    public function authenticate($username, $password) {
        $client = new Radius();
        $client->setSecret($this->cfg["shared_secret"]);

        $port = $this->cfg["port"] ?? DEFAULT_RADIUS_AUTHENTICATION_PORT;
        $client->setAuthenticationPort($port);

        $servers = is_array($this->cfg["radius_host"]) ? $this->cfg["radius_host"] : [$this->cfg["radius_host"]];
        $authenticated = $client->accessRequestList($servers, $username, $password, DEFAULT_RADIUS_TIMEOUT);

        if ($authenticated === false) {
            $message = $client->getErrorMessage();
            switch ($message) {
                case "Access rejected":
                    $this->error = get_translation("USER_OR_PASSWORD_INCORRECT");
                    break;
                default:
                    $this->error = get_translation("radius_error_occurred", [
                        "%error%" => $client->getErrorMessage(),
                        "%code%" => $client->getErrorCode()
                    ]);
                    break;
            }
        }
        return $authenticated;
    }

    public function getError() {
        return $this->error;
    }

}
