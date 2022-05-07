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
        NameSpaceResolver::validatePath($path);

        $wrapperClassName = $input->getArgument('className');
        $classProperties = $this->collectClassProperties($input, $output);

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Generate? ', true))) {
            return Command::FAILURE;
        }

        $this->classGenerator->setPath($path);
        $this->classGenerator->setClassProperties($classProperties);
        $this->classGenerator->setWrapperClassName($wrapperClassName);
        $this->classGenerator->writeClasses();

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

            $question = new Question('Property comment: ');
            $propertyComment = $helper->ask($input, $output, $question);

            $property = new Property($propertyName);
            $property->setType($propertyType);
            $property->setComment($propertyComment);

            $classProperties[] = $property;

            // confirm to add new property?
            $output->writeln('');

            $this->_displayCollectedProperties($classProperties, $output);

            $continue = new ConfirmationQuestion('Add another class property? ', true);

            if (!$helper->ask($input, $output, $continue)) {
                return $classProperties;
            }
        }
    }

    /**
     * @param Property[] $properties
     */
    protected function _displayCollectedProperties(array $properties, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['name', 'type', 'comment']);

        foreach ($properties as $property) {
            $table->addRow([$property->getName(), $property->getType(), $property->getComment()]);
        }

        $table->render();
    }
}
