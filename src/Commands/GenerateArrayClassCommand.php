<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Commands;

use Nette\PhpGenerator\Property;
use Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClassGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateArrayClassCommand extends Command
{
    protected static $defaultName = 'generate:dto-array';
    private ArrayClassGenerator $classGenerator;

    public function __construct(ArrayClassGenerator $classGenerator, string $name = null)
    {
        parent::__construct($name);
        $this->classGenerator = $classGenerator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate array-like data structure.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to generated classes.')
            ->addArgument('className', InputArgument::REQUIRED, 'Array class name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getHelper('question');
        $path = $input->getArgument('path');
        $wrapperClassName = $input->getArgument('className');
        $classProperties = $this->collectClassProperties($input, $output);

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Generate? ', true))) {
            return Command::FAILURE;
        }

        $this->classGenerator->setPath($path);
        $this->classGenerator->setClassProperties($classProperties);
        $this->classGenerator->setWrapperClassName($wrapperClassName);
        $this->classGenerator->generateClasses();

        return Command::SUCCESS;
    }

    /**
     * @return Property[]
     */
    protected function collectClassProperties(InputInterface $input, OutputInterface $output): array
    {
        $classProperties = [];
        $helper = $this->getHelper('question');
        while (true) {
            $question = new Question('Property name: ');
            $propertyName = $helper->ask($input, $output, $question);

            $question = new Question('Property type: ');
            $propertyType = $helper->ask($input, $output, $question);

            $property = new Property($propertyName);
            $property->setType($propertyType);
            $classProperties[] = $property;

            // confirm to add new property?
            $output->writeln('');
            $continue = new ConfirmationQuestion('Add another class property? ', true);

            if (!$helper->ask($input, $output, $continue)) {
                return $classProperties;
            }
        }
    }
}
