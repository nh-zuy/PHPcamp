<?php

namespace App\Core;

use App\Core\Middlewares\BaseMiddleware;

class Controller {
	public string $layout = "main";
	public array $middlewares = [];
	public string $action = '';

	public function setLayout($layout) {
		$this->layout = $layout;
	}

	public function render($view, $params = []) {
		return Application::$app->router->renderView($view, $params);
	}

	public function getMiddlewares() {
		return $this->middlewares;
	}

	public function registerMiddleware(BaseMiddleware $middleware) {
		array_push($this->middlewares, $middleware);
	}
}