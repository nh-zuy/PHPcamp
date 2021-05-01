<?php 

namespace App\Core;

class Session {

	public const FLASH_KEY = "flash";

	public function __construct() {
		session_start();

		$flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

		foreach ($flashMessages as $key => &$message) {
			$message["removed"] = true;
		};

		$_SESSION[self::FLASH_KEY] = $flashMessages;
	}

	public function set($key, $value) {
		
	}

	public function get($key) {
		
	}

	public function setFlash($key, $message) {
		$_SESSION[self::FLASH_KEY][$key] = [
			"removed" => false,
			"value" => $message
		];
	}

	public function getFlash($key) {
		return $_SESSION[self::FLASH_KEY][$key]["value"] ?? null;
	}

	public function __destruct() {
		$flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

		foreach ($flashMessages as $key => &$message) {
			if ($message["removed"] === true) {
				unset($flashMessages[$key]);
			};
		};

		$_SESSION[self::FLASH_KEY] = $flashMessages;
	}
}