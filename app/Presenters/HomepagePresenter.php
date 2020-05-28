<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * @var Nette\Database\Context
	 */
	private $database;

	/**
	 * HomepagePresenter constructor.
	 * @param Nette\Database\Context $database
	 */
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Rende data Default template
	 */
	public function renderDefault(): void
	{
		$this->template->task1 = $this->fetch($this->activeUsersWithCT4Sport());
		$this->template->task2 = $this->fetch($this->usersWithInactiveChannelsButActHBOGO());
		$this->template->task3 = $this->fetch($this->dateRangeOnChHBOGO());
	}

	public function fetch($query)
	{
		return [
			'data' => $this->database->fetchAll($query),
			'sql' => $query,
		];
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
				WHERE s.to = CURDATE() AND s.channelPackage IN (1,3,4,80)
					  OR s.to IS NULL	AND s.channelPackage IN (1,3,4,80)";
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
				OR s.to >= CURDATE() AND s.channelPackage = 82
				OR s.to IS NULL AND s.channelPackage = 82";
	}

	/**
	 * @return string
	 */
	protected function  dateRangeOnChHBOGO()
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
}

