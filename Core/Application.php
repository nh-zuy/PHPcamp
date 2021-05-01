<?php 

namespace App\Core;

use App\Core\Exception\ForbiddenException;

class Application {
	public static string $ROOT;

	public string $layout = "main";
	public Router $router;
	public Request $request;
	public Response $response;
	public Session $session;
	public Database $db;
	public static Application $app;
	public Controller $controller;
	public ?DBModel $user = null; 

	public function __construct($rootPath, array $config) {
		self::$ROOT = $rootPath;
		self::$app = $this;	
		$this->request = new Request();
		$this->response = new Response();
		$this->router = new Router($this->request, $this->response);
		$this->session = new Session();
		$this->db = new Database($config["db"]);
	}

	public function run() {

		try {
			echo $this->router->resolve();
		} catch (ForbiddenException $e) {
			Application::$app->router->renderView("403");
		}
	}

	public function getController() {
		return $this->controller;
	}

	public function setController(Controller $contr) {
		$this->controller = $contr;
	}

	public function login(DBModel $user) {
		$this->user = $user;

	}

	public static function isGuest() {
		return Application::$app->user === null;
	}
}