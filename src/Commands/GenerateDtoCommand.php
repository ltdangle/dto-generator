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
use function Psy\sh;

class GenerateDtoCommand extends Command
{
    protected static $defaultName = 'generate:dto-array';

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

        $classPath = $input->getArgument('classPath');
        $wrapperClassName = $input->getArgument('className');
        $itemClassName = $wrapperClassName . 'Item';

        $classProperties = $this->collectClassProperties($input, $output);

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Generate? ', true))) {
            return Command::FAILURE;
        }

        $this->generateItemClassFile($classPath, $itemClassName, $classProperties);

        return Command::SUCCESS;
    }

    /**
     * @param ClassPropertyEntity[] $classProperties
     */
    protected function generateItemClassFile(string $path, string $className, array $classProperties)
    {
        $namespaceResolver = new NameSpaceResolver(realpath(\Composer\InstalledVersions::getRootPackage()['install_path']));

        $namespace = $namespaceResolver->path2Namespace($path);

        $classEntity = new ClassEntity();
        $classEntity->setName($className);
        $classEntity->setNamespace($namespace);
        $classEntity->setComment('Array item.');
        foreach ($classProperties as $classProperty) {
            $classEntity->addClassProperty($classProperty);
        }
        $service = new ArrayItemClassGenerator($classEntity);

        $file = $service->generateFile();

        $printer = new PsrPrinter();
        eval(sh());
        file_put_contents("$path/$className.php", $printer->printFile($file));


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