<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Models\LoginForm;
use App\Core\Middlewares\AuthMiddleware;

/**
 *
 *
 *
**/
class AuthController extends Controller {

	public function __construct() {
		//$this->registerMiddleware(new AuthMiddleware(["login"]));
	}
	/**
	 *
	 *
	**/
	public function login(Request $request, Response $response) {
		$this->setLayout("auth");

		$model = new LoginForm();

		if ($request->isPost()) {
			$model->loadData($request->getBody());

			if ($model->validate() && $model->login()) {
				$response->redirect("/");
				return;
			}
		};

		return $this->render("login", [
			"model" => $model
		]);
	}

	public function register(Request $request) {
		$model = new User();

		if ($request->isPost()) {
			$model->loadData($request->getBody());

			if ($model->validate() && $model->register()) {
				return "Success";
			};

			return $this->render("register", [
				'model' => $model
			]);
		};
		
		$this->setLayout("auth");
		return $this->render("register", [
			'model' => $model
		]);
	}
}