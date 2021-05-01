<?php

namespace App\Models;

use App\Core\DBModel;
use App\Core\Model;
use App\Core\Application;
use App\Model\User;

class LoginForm extends Model {

	public string $username;
	public string $password;

	public function tableName(): string {
		return "loginForm";
	}

	public function rules(): array {
		return [
			"username" => [
				self::RULE_REQUIRED
			],
			"password" => [
				self::RULE_REQUIRED
			]
		];
	}

	public function labels(): array {
		return [
			"username" => "Username",
			"password" => "Password"
		];
	}

	public function errorMessages(): array {
		return [
		];
	}

	public function login() {
		$user = User::findOne(["email" => $this->email]);

		if (!$user) {
			$this->addError("email", self::RULE_REQUIRED);
		};

		if (!password_verify($this->password, $user->password)) {
			$this->addError("password", self::RULE_REQUIRED);
		};

		return Application::$app->login($user);
	}
} 