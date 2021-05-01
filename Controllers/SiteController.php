<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class SiteController extends Controller {

	public function home() {
		$params = [
			"name" => "Zuy"
		];

		return $this->render("home", $params);
	}

	public function contact() {
		return $this->render("contact");
	}

	public function handleContact(Request $request) {
		$body = $request->getBody();

		die(var_dump($body));

	}
}