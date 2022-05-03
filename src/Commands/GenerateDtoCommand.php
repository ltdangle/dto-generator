<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Commands;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateDtoCommand extends Command
{
    protected static $defaultName = 'generate:dto-array';

    private string $classPath;
    private string $wrapperClassName;
    private string $itemClassName;

    /**
     * @var ClassPropertyEntity[]
     */
    private array $classProperties;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
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

        $this->classPath = $input->getArgument('classPath');
        $this->wrapperClassName = $input->getArgument('className');
        $this->itemClassName = $this->wrapperClassName . 'Item';

        $this->collectClassProperties($input, $output);
        $this->printClassProperties($output);

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Generate? ', true))) {
            return Command::SUCCESS;
        }

        $itemClassFile = new PhpFile();
        $itemClassFile->addNamespace('SomeNamespace');
        $itemClassFile->addComment('This file is auto-generated.');
        $itemClassFile->setStrictTypes();
        $itemClass = $itemClassFile->addClass($this->itemClassName);
        $this->_buildItemClass($itemClass);

        $wrapperClassFile = new PhpFile();
        $itemClassFile->addNamespace('SomeNamespace');
        $wrapperClassFile->addComment('This file is auto-generated.');
        $wrapperClassFile->setStrictTypes();
        $wrapperClass = $wrapperClassFile->addClass($this->wrapperClassName);
        $this->_buildWrapperClass($wrapperClass, $itemClass);

        $output->writeln($itemClassFile);
        $output->writeln($wrapperClassFile);

        file_put_contents($itemClass->getName() . '.php', $itemClassFile);
        file_put_contents($wrapperClass->getName() . '.php', $wrapperClassFile);

        return Command::SUCCESS;
    }

    private function collectClassProperties(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        while (true) {
            $question = new Question('Property name: ');
            $propertyName = $helper->ask($input, $output, $question);

            $question = new Question('Property type: ');
            $propertyType = $helper->ask($input, $output, $question);

            $this->classProperties[] = new ClassPropertyEntity($propertyName, $propertyType);

            // confirm to add new property?
            $output->writeln('');
            $continue = new ConfirmationQuestion('Add another class property? ', true);

            if (!$helper->ask($input, $output, $continue)) {
                return;
            }
        }
    }

    private function printClassProperties(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('Class properties:');
        foreach ($this->classProperties as $classProperty) {
            $output->writeln('private $' . $classProperty->getPropertyType() . ' ' . $classProperty->getPropertyName() . ';');
        }
    }

    private function _buildWrapperClass(ClassType $wrapperClass, ClassType $itemClass)
    {
        $wrapperClass
            ->addProperty('items')
            ->setType('array')
            ->addComment("@var {$itemClass->getName()}[]");
    }

    private function _buildItemClass(ClassType $class)
    {
        foreach ($this->classProperties as $classProperty) {
            $class->addProperty($classProperty->getPropertyName())->setType($classProperty->getPropertyType());
        }
    }
}