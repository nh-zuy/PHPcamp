<?php

namespace App\Core;

/**
|
|
|
**/
abstract class DBModel extends Model {
	abstract public function tableName(): string;

	abstract public function attributes(): array;

	abstract public function labels(): array;

	public function save() {
		$tableName = $this->tableName();
		$attributes = $this->attributes();
		$params = array_map(function($attr) {
			return ":$attr";
		}, $attributes);

		$stmt = self::prepare(
			"INSERT INTO {$tableName} (" . 
			implode(",", $attributes) .
			") VALUES(" . 
			implode(",", $params) . ")"
		);

		foreach ($attributes as $attr) {
			$stmt->bindValue(":$attr", $this->{$attr});
		};
		$stmt->execute();
		return true;
	}

	public static function prepare($sql) {
		return $this->db->pdo->prepare($sql);
	}

	public static function findOne($where) {
		$tableName = $this->tableName();
		$attributes = array_keys($where);

		$WHERE = "WHERE " . implode("AND", array_map(function($attr) {
			return "{$attr} = :{$attr}";
		}, $attributes));

		$sql = "SELECT * FROM {$tableName} " . $WHERE;

		$stmt = self::prepare($sql);

		foreach ($where as $key => $value) {
			$stmt->bindValue(":{$key}", $value);
		};
		$stmt->execute();

		return $stmt->fetchObject(static::class); 
	}
}