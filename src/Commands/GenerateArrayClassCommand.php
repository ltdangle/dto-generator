<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Commands;

use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClass\ArrayItemClassGenerator;
use Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClass\ArrayWrapperClassGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateArrayClassCommand extends Command
{
    protected static $defaultName = 'generate:dto-array';
    private ArrayItemClassGenerator $arrayItemClassGenerator;
    private ArrayWrapperClassGenerator $arrayWrapperClassGenerator;

    public function __construct(ArrayItemClassGenerator $arrayItemClassGenerator, ArrayWrapperClassGenerator $arrayWrapperClassGenerator, string $name = null)
    {
        parent::__construct($name);
        $this->arrayItemClassGenerator = $arrayItemClassGenerator;
        $this->arrayWrapperClassGenerator = $arrayWrapperClassGenerator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate array-like data structure.')
            ->addArgument('classPath', InputArgument::REQUIRED, 'classPath')
            ->addArgument('className', InputArgument::REQUIRED, 'className');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getHelper('question');

        $classPath = $input->getArgument('classPath');
        $wrapperClassName = $input->getArgument('className');
        $itemClassName = $wrapperClassName.'Item';

        $arrayProperties = $this->collectClassProperties($input, $output);

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Generate? ', true))) {
            return Command::FAILURE;
        }

        // generate array item class
        $itemClassEntity = new ClassEntity();
        $itemClassEntity->setClassComment("$wrapperClassName item.");
        $itemClassEntity->setPath($classPath);
        $itemClassEntity->setName($itemClassName);
        foreach ($arrayProperties as $classProperty) {
            $itemClassEntity->addClassProperty($classProperty);
        }
        $this->arrayItemClassGenerator->generateFile($itemClassEntity);

        // generate array wrapper class
        $wrapperClassEntity = new ClassEntity();
        $wrapperClassEntity->setClassComment("$wrapperClassName array-like structure.");
        $wrapperClassEntity->setPath($classPath);
        $wrapperClassEntity->setName($wrapperClassName);
        $wrapperClassEntity->addClassProperty(new ClassPropertyEntity('items', 'array'));
        $this->arrayWrapperClassGenerator->generateFile($wrapperClassEntity);

        return Command::SUCCESS;
    }

    /**
     * @return ClassPropertyEntity[]
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

            $classProperties[] = new ClassPropertyEntity($propertyName, $propertyType);

            // confirm to add new property?
            $output->writeln('');
            $continue = new ConfirmationQuestion('Add another class property? ', true);

            if (!$helper->ask($input, $output, $continue)) {
                return $classProperties;
            }
        }
    }
}
