<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2023, githubkoma
 *
 * @author github koma
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\WebPush\Command;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IUser;
use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OCA\WebPush\Service\WebPushLibraryService;

class Generate extends Command {
	/** @var ITimeFactory */
	protected $timeFactory;

	/** @var IUserManager */
	protected $userManager;

	/** @var WebPushLibraryService */
    private $webPushLibraryService;

	public function __construct(ITimeFactory $timeFactory,
		IUserManager $userManager,
        WebPushLibraryService $webPushLibraryService) {
		parent::__construct();

		$this->timeFactory = $timeFactory;
		$this->userManager = $userManager;
        $this->webPushLibraryService = $webPushLibraryService;
	}

	protected function configure(): void {
		$this
			->setName('webpush:generate')
			->setDescription('Generate a WebPush notification for the given user')
			->addArgument(
				'user-id',
				InputArgument::REQUIRED,
				'User ID of the user to notify'
			)
			->addArgument(
				'message',
				InputArgument::REQUIRED,
				'Short message to be sent to the user (max. 255 characters)'
			)
		;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$userId = $input->getArgument('user-id');
		$message = $input->getArgument('message');

        $subject = "Admin Message";

		$user = $this->userManager->get($userId);
		if (!$user instanceof IUser) {
			$output->writeln('Unknown user');
			return 1;
		}

		$datetime = $this->timeFactory->getDateTime();

		try {

            $this->webPushLibraryService->webpushToUser($userId, $subject, $message, $action = "", $actionURL = "");

		} catch (\Exception $e) {
			$output->writeln('Error while sending the webpush notification');
			return 1;
		}

		return 0;
	}
}
