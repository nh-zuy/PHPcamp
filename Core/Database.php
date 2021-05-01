<?php

namespace App\Core;

use App\Core\Application;
use PDO;


/**
 * ----------------------------------------
 * |
 * |
 * |
 * ----------------------------------------
 **/
class Database {
	/* Singleton PDO in entire application */
	public PDO $pdo;

	/**
	 * ----------------------------------------------
	 * |
	 * |
	 * ----------------------------------------------
	 **/
	public function __construct(array $config = []) {
		$dsn = $config['dsn'] ?? '';
		$user = $config['user'] ?? '';
		$password = $config['password'] ?? '';
		
		$this->pdo = new PDO($dsn, $user, $password);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * ----------------------------------------------
	 * |
	 * |
	 * ----------------------------------------------
	 **/
	public function applyMigrations() {
		/* First create tbl_migrations in DB */
		$this->createMigrationsTable();

		/* Select all migrations've been applied */
		$appliedMigrations = $this->getAppliedMigrations();

		/* Scan /migrations to select all migration files */
		$fileMigrations = scandir(Application::$ROOT . "/migrations");

		/* Compare 2 array to take new migration files */
		$willApplyMigrations = array_diff($fileMigrations, $appliedMigrations);
		$willApplyMigrations = array_diff($willApplyMigrations, [".", ".."]);

		/* Apply all new migration files */
		$newMigrations = [];
		foreach ($willApplyMigrations as $migration) {
			/* Take the migration file */
			require_once(Application::$ROOT . "/migrations/{$migration}");
			/* Take the class and up the migration */
			$className = pathinfo($migration, PATHINFO_FILENAME);
			$newMigration = new $className();
			$newMigration->up();

			array_push($newMigrations, $migration);
		};

		/* Saving all to the DB */
		if (!empty($newMigrations)) {
			$this->saveMigrations($newMigrations);
			echo "saved";
		};
	}

	/**
	 * ----------------------------------------------
	 * |
	 * |
	 * ----------------------------------------------
	 **/
	private function createMigrationsTable() {
		/* All migrations must be save in tbl_migrations */
		$this->pdo->exec(
			"CREATE TABLE IF NOT EXISTS migrations (
				id INT AUTO_INCREMENT PRIMARY KEY,
				migration VARCHAR(255),
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
			) ENGINE=INNODB;"
		);
	}

	/**
	 * ----------------------------------------------
	 * |
	 * |
	 * ----------------------------------------------
	 **/
	private function getAppliedMigrations() {
		$stmt = $this->pdo->prepare("SELECT migration FROM migrations");
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * ----------------------------------------------
	 * |
	 * |
	 * ----------------------------------------------
	 **/
	private function saveMigrations(array $newMigrations = []) {
		$values = implode(",", array_map(function($migration) {
			return "('{$migration}')";
		}, $newMigrations));

		$stmt = $this->pdo->prepare("INSERT INTO migrations(migration) VALUES {$values}");
		$stmt->execute();
	}

	/**
	 * ----------------------------------------------
	 * |
	 * |
	 * ----------------------------------------------
	 **/
	public function prepare($sql) {
		return $this->pdo->prepare($sql);
	}
}