<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\TaskManager;
use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

	/** @var TaskManager */
	private $taskManager;


	public function __construct(TaskManager $taskManager)
	{
		$this->taskManager = $taskManager;
	}


	/**
	 * Render data Default template
	 */
	public function renderDefault(): void
	{
		$this->template->task1 = $this->taskManager->getTask1();
		$this->template->task2 =  $this->taskManager->getTask2();
		$this->template->task3 =  $this->taskManager->getTask3();
	}



}

