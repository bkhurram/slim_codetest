<?php

namespace App\Application\Console;

use App\Application\Models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UserCreateCommand extends Command
{
	protected function configure(): void
	{
		parent::configure();

		$this->setName('user:create');
		$this->setDescription('Create a new user: user:create <givenName> <familyName> <email> <password>')
			->addArgument('givenName', InputArgument::REQUIRED, 'The user given name')
			->addArgument('familyName', InputArgument::REQUIRED, 'The user family name')
			->addArgument('email', InputArgument::REQUIRED, 'The user email address')
			->addArgument('password', InputArgument::REQUIRED, 'The user password');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		// Example logic for creating a user
		$givenName = $input->getArgument('givenName');
		$familyName = $input->getArgument('familyName');
		$email = $input->getArgument('email');
		$password = $input->getArgument('password');

		$user = new User();
		$user->givenName = $givenName;
		$user->familyName = $familyName;
		$user->email = $email;
		$user->password = password_hash($password, PASSWORD_BCRYPT);
		$user->save();

		$output->writeln("User created: $givenName $familyName ($email)");

		return Command::SUCCESS;
	}
}
