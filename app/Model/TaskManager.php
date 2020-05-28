<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\Context;

class TaskManager
{
	use Nette\SmartObject;

	/** @var Context */
	private $database;

	/**
	 * TaskManager constructor.
	 * @param Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}

	/**
	 * @return array
	 */
	public function getTask1()
	{
		return $this->fetch($this->activeUsersWithCT4Sport());
	}

	/**
	 * @return array
	 */
	public function getTask2()
	{
		return $this->fetch($this->usersWithInactiveChannelsButActHBOGO());
	}

	/**
	 * @return array
	 */
	public function getTask3()
	{
		return $this->fetch($this->dateRangeOnChHBOGO());
	}

	/**
	 * @return string
	 */
	protected function activeUsersWithCT4Sport()
	{
		return "SELECT DISTINCT
					u.id,
					u.login,
					u.fullName	
				FROM users u
				JOIN services s
					ON u.id = s.user
				WHERE (s.to = CURDATE() OR s.to IS NULL) AND s.channelPackage IN (1,3,4,80)";
	}

	/**
	 * @return string
	 */
	protected function usersWithInactiveChannelsButActHBOGO()
	{
		return "SELECT DISTINCT
					u.id,
					u.login,
					u.fullName
				FROM users u
				JOIN services s
					ON u.id = s.user
				WHERE s.to < CURDATE() 
				OR (s.to >= CURDATE() AND s.channelPackage = 82) 
				OR (s.to IS NULL AND s.channelPackage = 82)";
	}

	/**
	 * @return string
	 */
	protected function dateRangeOnChHBOGO()
	{
		return "SELECT
					u.id,
					u.login,
					u.fullName,
					sum(timestampdiff(
						day, 
						s.from, 
						IF(
							s.to IS NOT NULL,
							s.to,
							CURDATE()
						))) as daterange
				FROM users u
				JOIN services s
					ON u.id = s.user
				AND s.channelPackage IN (3,80,82)
				GROUP BY u.id";
	}

	/**
	 * @param $query
	 * @return array
	 */
	public function fetch($query)
	{
		return [
			'data' => $this->database->fetchAll($query),
			'sql' => $query,
		];
	}
}

