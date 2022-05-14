<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Commands;

use Nette\PhpGenerator\Property;
use Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClassGenerator;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateArrayClassCommand extends Command
{
    protected static $defaultName = 'generate:dto-array';
    protected ArrayClassGenerator $classGenerator;
    /** Interactive commands */
    protected array $commands = [];

    protected string $arrayClassName = '';
    protected string $arrayClassPath = '';
    /** @var Property[] */
    protected array $arrayProperties = [];

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
        // configure interactive commands
        $this->commands['a'] = function () use ($input, $output) {
            $this->addPropertyDialog($input, $output);
        };
        $this->commands['d'] = function () use ($input, $output) {
            $this->deletePropertyDialog($input, $output);
        };
        $this->commands['g'] = function () use ($input, $output) {
            $this->generateClassesCommand($input, $output);
        };

        // init command properties
        $this->arrayClassPath = $input->getArgument('path');
        NameSpaceResolver::validatePath($this->arrayClassPath);
        $this->arrayClassName = $input->getArgument('className');

        $this->loop($input, $output);

        return Command::SUCCESS;
    }

    protected function loop(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $this->commands['a']();
        $this->_displayCollectedProperties($output);

        $action = new Question('(A)dd another property, (D)elete property, (G)enerate classes, or (Q)uit? ');

        while ($answer = strtolower($helper->ask($input, $output, $action))) {
            if ('q' === $answer) {
                break;
            }

            if (!array_key_exists($answer, $this->commands)) {
                continue;
            }

            $this->commands[$answer]();

            $this->_displayCollectedProperties($output);
        }
    }

    protected function generateClassesCommand(InputInterface $input, OutputInterface $output)
    {
        $this->classGenerator->setPath($this->arrayClassPath);
        $this->classGenerator->setClassProperties($this->arrayProperties);
        $this->classGenerator->setWrapperClassName($this->arrayClassName);
        $generated = $this->classGenerator->writeClasses();
        $output->writeln('Generated:');
        $output->writeln("<fg=green>{$generated->getArrayWrapperClassPath()}</>");
        $output->writeln("<fg=green>{$generated->getItemClassPath()}</>");
    }

    protected function addPropertyDialog(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Property name: ');
        $propertyName = $helper->ask($input, $output, $question);

        $question = new Question('Property type: ');
        $propertyType = $helper->ask($input, $output, $question);

        $question = new Question('Property comment: ');
        $propertyComment = $helper->ask($input, $output, $question);

        $property = new Property($propertyName);
        $property->setVisibility('private');
        $property->setType($propertyType);
        $property->setComment($propertyComment);

        $this->arrayProperties[] = $property;

        // confirm to add new property?
        $output->writeln('');
    }

    protected function deletePropertyDialog(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Select property to delete:',
            array_map(function (Property $property) {
                return $property->getName();
            }, $this->arrayProperties),
        );
        $question->setErrorMessage('Option %s is invalid.');
        $propertyName = $helper->ask($input, $output, $question);

        // remove property
        foreach ($this->arrayProperties as $key => $arrayProperty) {
            if ($propertyName === $arrayProperty->getName()) {
                unset($this->arrayProperties[$key]);
            }
        }

    }

    protected function _displayCollectedProperties(OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['name', 'type', 'comment']);

        foreach ($this->arrayProperties as $property) {
            $table->addRow([$property->getName(), $property->getType(), $property->getComment()]);
        }

        $table->render();
    }
}
