<?php 

namespace App\Core;

class Router {
	public Request $request;
	public Response $response;
	protected array $routes = [];

	public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
	}

	public function get($path = "/", $callback) {
		$this->routes['get'][$path] = $callback;
	}

	public function post($path = "/", $callback) {
		$this->routes['post'][$path] = $callback;
	}


	public function resolve() {
		$path = $this->request->getPath();
		$method = $this->request->method();
		$callback = $this->routes[$method][$path] ?? false;

		if ($callback === false) {
			$this->response->setStatusCode(404);
			return $this->renderView("404");
		};

		if (is_string($callback)) {
			return $this->renderView($callback);
		};

		if (is_array($callback)) {
			$controller = new $callback[0]();
			Application::$app->controller = $controller;
			$controller->action = $callback[1];
			$callback[0] = $controller;

			foreach ($controller->getMiddlewares() as $middleware) {
				$middleware->execute();
			}
		}

		return call_user_func($callback, $this->request, $this->response);
	}

	public function renderView($view, $params = []) {
		$layout = $this->layoutContent();
		$content = $this->renderOnlyView($view, $params);
		return str_replace("{{content}}", $content, $layout);
	}

	public function renderContent($view) {
		$layout = $this->layoutContent();
		return str_replace("{{content}}", $view, $layout);
	}

	protected function layoutContent() {
		$layout = Application::$app->layout;

		if (Application::$app->controller) {
			$layout = Application::$app->controller->layout;	
		};

		ob_start();
		include_once(Application::$ROOT."/Views/layouts/{$layout}.php");
		return ob_get_clean();
	}

	protected function renderOnlyView($view, $params) {
		foreach ($params as $key => $value) {
			$$key = $value;
		};

		include_once(Application::$ROOT."/Views/{$view}.php");
		return ob_get_clean();
	}
}