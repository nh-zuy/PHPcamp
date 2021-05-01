<?php

namespace App\Models;

use App\Core\DBModel;

/**
 * 
 */
class User extends DBModel
{
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_DELETED = 2;

	public string $lastname;
	public string $email;
	public string $username;
	public string $password;
	public string $passwordConfirm;
	public int $status = self::STATUS_INACTIVE;

	public function tableName(): string {
		return "users";
	}

	public function labels(): array {
		return [
			"lastname" => "Lastname",
			"email" => "Email",
			"username" => "Username",
			"password" => "Password",
			"passwordConfirm" => "Confirm Password"
		];
	}

	public function attributes(): array {
		return [
			'lastname',
			'email',
			'username',
			'password',
			'status'
		];
	}

	public function rules(): array {
		return [
			"lastname" => [
				self::RULE_REQUIRED
			],

			"email" => [
				self::RULE_REQUIRED, 
				self::RULE_EMAIL,
				[
					self::RULE_UNIQUE,
					"class" => self::class
				]
			],

			"username" => [
				self::RULE_REQUIRED
			],

			"password" => [
				self::RULE_REQUIRED,
				[ self::RULE_MIN, "min" => 8 ],
				[ self::RULE_MAX, "max" => 30 ]
			],
			
			"passwordConfirm" => [
				self::RULE_REQUIRED,
				[ self::RULE_MATCH, "match" => "password" ]
			]
		];
	}

	public function errorMessages():array {
		return [
			self::RULE_REQUIRED => "This field is required",
			self::RULE_EMAIL => "This field must be a valid email address",
			self::RULE_MIN => "Min length of this field must be {min}",
			self::RULE_MAX => "Max length of this field must be {max}",
			self::RULE_MATCH => "This field must be the same of {match}",
			self::RULE_UNIQUE => "This {field} have already existed"
		];
	}

	public function register() {
		$this->status = STATUS_INACTIVE;
		$this->password = password_hash($this->password, PASSWORD_DEFAULT);

		return $this->save();
	}
}