<?php

namespace App\Core;

use App\Core\Application;

/**
 * -----------------------------
 * | Abstract class. Any model must be derrived it 
 * |
 * | @package App\Core
 * | 
 * -----------------------------
**/
abstract class Model {

	/**
	 * ------------------------------------
	 * ----- Rule of field
	 * ------------------------------------
	**/
	public const RULE_REQUIRED = 'required';
	public const RULE_EMAIL = 'email';
	public const RULE_MIN = 'min';
	public const RULE_MAX = 'max';
	public const RULE_MATCH = 'match';
	public const RULE_UNIQUE = "unique";

	/**
	 * ------------------------------
	 * --- List error
	 * ------------------------------
	**/
	public array $errors = [];

	/**
	 * ----------------------------------------
	 * --- Abstract function
	 * --- The child need implement this method
	 * --- to build a rule for input
	 * ----------------------------------------
	**/
	abstract public function rules(): array;

	/**
	 * -------------------------------------
	 * --- Message pop out when error occurs
	 * -------------------------------------
	**/
	abstract public function errorMessages(): array;

	/**
	 * ---------------------------------------
	 * --- Making error
	 * ---------------------------------------
	**/
	public function addError(string $attribute, string $rule, array $params = []) {
		$message = $this->errorMessages()[$rule] ?? '';
		foreach ($params as $key => $value) {
			$message = str_replace("{{$key}}", $value, $message);
		};
		$this->errors[$attribute][] = $message;
	}

	/**
	 * ---------------------------------------
	 * --- Making error
	 * ---------------------------------------
	**/
	public function hasError($attribute) {
		return array_key_exists($attribute, $this->errors);
	}

	/**
	 *
	 *
	 *
	**/
	public function loadData($data = []) {
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 *
	 *
	 *
	**/
	public function validate() {
		foreach ($this->rules() as $attribute => $rules) {
			$value = $this->{$attribute};

			foreach ($rules as $rule) {
				$ruleName = $rule;

				if (!is_string($ruleName)) {
					$ruleName = $rule[0];
				};

				if($ruleName === self::RULE_REQUIRED && !$value) {
					$this->addError($attribute, self::RULE_REQUIRED);
				};

				if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$this->addError($attribute, self::RULE_EMAIL);
				};

				if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
					$this->addError($attribute, self::RULE_MIN, $rule);
				};

				if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
					$this->addError($attribute, self::RULE_MAX, $rule);
				};

				if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
					$this->addError($attribute, self::RULE_MATCH, $rule);
				};

				if ($ruleName === self::RULE_UNIQUE) {
					$className = $rule['class'];
					$uniqueAttr = $rule['attribute'] ?? $attribute;
					$tableName = self::tableName();

					$stmt = Application::$app->db->prepare("SELECT * FROM {$tableName} WHERE {$uniqueAttr} = :{$uniqueAttr}");
					$stmt->bindValue(":{$uniqueAttr}", $value);
					$stmt->execute();
					$record = $stmt->fetchObject();

					if ($record) {
						$this->addError($attribute, self::RULE_UNIQUE, ["field" => $attribute ]);
					};
				}
			}
		};

		return empty($this->errors);
	}

	
}