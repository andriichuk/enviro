<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderFactory;
use Andriichuk\Enviro\Verification\SpecVerificationService;
use Andriichuk\Enviro\Validation\EmailValidator;
use Andriichuk\Enviro\Validation\EnumValidator;
use Andriichuk\Enviro\Validation\EqualsValidator;
use Andriichuk\Enviro\Validation\IntegerValidator;
use Andriichuk\Enviro\Validation\RequiredValidator;
use Andriichuk\Enviro\Validation\ValidatorRegistry;
use Andriichuk\Enviro\Writer\Env\EnvFileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VerifyCommand extends Command
{
    protected static $defaultName = 'verify';

    protected function configure(): void
    {
        $this
            ->addArgument('env', InputArgument::REQUIRED, 'The name of the environment to be verified.')
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED, 'Dotenv file path to check.', '.env')
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'Dotenv specification file path.', 'env.spec.yaml')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment variables according to specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $validatorRegistry = new ValidatorRegistry();
        $validatorRegistry->add(new IntegerValidator());
        $validatorRegistry->add(new EmailValidator());
        $validatorRegistry->add(new EnumValidator());
        $validatorRegistry->add(new EqualsValidator());
        $validatorRegistry->add(new RequiredValidator());

        $factory = new SpecificationReaderFactory();
        $reader = $factory->basedOnResource($input->getOption('spec'));

        $service = new SpecVerificationService(
            new EnvFileWriter($input->getOption('env-file')),
            $reader,
            $validatorRegistry,
        );

        $io = new SymfonyStyle($input, $output);
        $io->title("Start checking the content of the file...");
        $io->listing([
            "Environment name: <info>{$input->getArgument('env')}</info>.",
            "Environment file: <info>{$input->getOption('env-file')}</info>.",
            "Environment specification: <info>{$input->getOption('spec')}</info>.",
        ]);

        try {
            $messages = $service->verify($input->getOption('spec'), $input->getArgument('env'));
        } catch (Throwable $exception) {
            $output->writeln("Error: {$exception->getMessage()}");

            return Command::FAILURE;
        }

        if ($messages !== []) {
            $list = [];

            foreach ($messages as $key => $message) {
                $list[] = "<bg=red;options=bold>$key</> $message";
            }

            $io->section('Found errors:');
            $io->listing($list);
            $io->error('Application environment is not valid.');

            return Command::FAILURE;
        }

        $io->success('Application environment is valid.');

        return Command::SUCCESS;
    }
}
