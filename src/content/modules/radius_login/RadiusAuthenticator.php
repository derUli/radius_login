<?php
class RadiusAuthenticator {
	private $mainClass;
	private $cfg;
	private $error = null;
	public function __construct($mainClass) {
		$this->mainClass = $mainClass;
		$this->cfg = $mainClass->getConfiguration ();
	}
	public function authenticate($username, $password) {
		$radius = radius_auth_open ();
		if (is_array ( $this->cfg ["radius_host"] )) {
			foreach ( $this->cfg ["radius_host"] as $host ) {
				radius_add_server ( $radius, $host, $this->cfg ["port"] ?? DEFAULT_RADIUS_AUTHENTICATION_PORT, $this->cfg ["shared_secret"], DEFAULT_RADIUS_TIMEOUT, DEFAULT_MAX_TRIES );
				$this->mainClass->debug ( "RADIUS Server: $host" );
			}
		} else {
			radius_add_server ( $radius, $this->cfg ["radius_host"], $this->cfg ["port"] ?? DEFAULT_RADIUS_AUTHENTICATION_PORT, $this->cfg ["shared_secret"], DEFAULT_RADIUS_TIMEOUT, DEFAULT_MAX_TRIES );
			$this->mainClass->debug ( "RADIUS Server: " . $this->cfg ["radius_host"] );
		}
		radius_create_request ( $radius, RADIUS_ACCESS_REQUEST );
		radius_put_attr ( $radius, RADIUS_USER_NAME, $username );
		radius_put_attr ( $radius, RADIUS_USER_PASSWORD, $password );
		
		$result = radius_send_request ( $radius );
		
		$this->error = null;
		switch ($result) {
			case RADIUS_ACCESS_ACCEPT :
				return true;
				break;
			case RADIUS_ACCESS_REJECT :
				// An Access-Reject response to an Access-Request indicating that the RADIUS server could not authenticate the user.
				$this->error = get_translation ( "USER_OR_PASSWORD_INCORRECT" );
				return false;
				break;
			case RADIUS_ACCESS_CHALLENGE :
				$this->error = get_translation ( "challenge_required" );
			default :
				// TODO: Fehlerstrins von libradius benutzerfreundlich aufbereiten
				$this->error = get_translation ( "radius_error_occurred", array (
						"%error%" => radius_strerror ( $radius ) 
				) );
				break;
		}
		return false;
	}
	public function getError() {
		return $this->error;
	}
}