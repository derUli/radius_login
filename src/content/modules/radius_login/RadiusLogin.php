<?php

class RadiusLogin extends Controller {

    // Initialize logging if enabled
    public function beforeInit() {
        $cfg = $this->getConfiguration();
        $logPath = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/log/radius_login");
        if (isset($cfg["log_enabled"]) and $cfg["log_enabled"]) {
            if (!file_exists($logPath)) {
                mkdir($logPath, null, true);
            }
            $this->logger = new Katzgrau\KLogger\Logger($logPath, Psr\Log\LogLevel::DEBUG, array(
                "extension" => "log"
            ));
        }
    }

    // get radius_login configuration from config file
    public function getConfiguration() {
        $cfg = new CMSConfig();
        if (isset($cfg->radius_login)) {
            return $cfg->radius_login;
        }
        return null;
    }

    // handle authentication
    public function sessionDataFilter($sessionData) {
        // empty passwords are not supported
        if (empty($_POST["user"]) or empty($_POST["password"]) or ! $this->getConfiguration()) {
            return $sessionData;
        }
        $username = trim($_POST["user"]);
        $cfg = $this->getConfiguration();

        $skip_on_error = (isset($cfg["skip_on_error"]) and $cfg["skip_on_error"]);
        if ($skip_on_error) {
            $this->debug("skip_on_error is enabled");
        } else {
            $sessionData = false;
        }
        $error = null;
        $authenticator = new RadiusAuthenticator($this);
        $success = $authenticator->authenticate($username, $_POST["password"]);
        if ($success) {
            $user = getUserByName($username);
            $email = $username . "@" . $cfg["mail_suffix"];
            // Create user if it doesn't exists (if create_user is enabled)
            if (!$user and isset($cfg["create_user"]) and $cfg["create_user"]) {
                $this->debug("User $username doesn't exists. Create it.");

                $user = new User();
                $user->setUsername($username);
                $user->setLastname($cfg["default_lastname"] ?? "Doe");
                $user->setFirstname($cfg["default_firstname"] ?? "John");
                $user->setEmail($email);
                $user->setPassword($_POST["password"]);
                $user->setPrimaryGroupId(Settings::get("default_acl_group") ? intval(Settings::get("default_acl_group")) : null );
                $user->save();
            }
            $user = getUserByName($username);
            if ($user) {
                // Sync RADIUS password to UliCMS users database (if sync_passwords is enabled)
                if (isset($cfg["sync_passwords"]) and $cfg["sync_passwords"]) {
                    $pwdUser = new User();
                    $pwdUser->loadByUsername($username);
                    if (!$pwdUser->checkPassword($_POST["password"])) {
                        $this->debug("Password of $username was changed. Syncronize password.");
                        $pwdUser->setPassword($_POST["password"]);
                        $pwdUser->save();
                    }
                }
                $this->debug("User Login $username OK.");

                return $user;
            }
        } else {
            if (isset($cfg["skip_on_error"]) and $cfg["skip_on_error"]) {
                $this->debug("Fallback to internal user login");
                return validate_login($username, $_POST["password"]);
            }
            $error = $authenticator->getError();
        }
        // if skip_on_error is enabled it will try to authenticate the user locally if RADIUS authentication fails
        if ($error) {
            $this->error("RADIUS Login Error: $error");
            $_REQUEST["error"] = $error;
        }
        return $sessionData;
    }

    public function debug($message, $context = array()) {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }

    public function info($message, $context = array()) {
        if ($this->logger) {
            $this->logger->info($message, $context);
        }
    }

    public function error($message, $context = array()) {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }

}
