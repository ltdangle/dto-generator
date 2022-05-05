<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Commands;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\ArrayClass\ArrayItemClassGenerator;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateDtoCommand extends Command
{
    protected static $defaultName = 'generate:dto-array';
    private ArrayItemClassGenerator $arrayItemClassGenerator;

    public function __construct(ArrayItemClassGenerator $arrayItemClassGenerator, string $name = null)
    {
        parent::__construct($name);
        $this->arrayItemClassGenerator = $arrayItemClassGenerator;
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
        $itemClassName = $wrapperClassName . 'Item';

        $arrayProperties = $this->collectClassProperties($input, $output);

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Generate? ', true))) {
            return Command::FAILURE;
        }

        $classEntity = new ClassEntity();
        $classEntity->setPath($classPath);
        $classEntity->setName($itemClassName);
        foreach ($arrayProperties as $classProperty) {
            $classEntity->addClassProperty($classProperty);
        }
        $this->arrayItemClassGenerator->generateFile($classEntity);

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