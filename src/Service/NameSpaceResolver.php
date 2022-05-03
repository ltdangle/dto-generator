<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service;

use InvalidArgumentException;
use function Psy\sh;

class NameSpaceResolver
{
    private array $psr4;

    public function addPsr4Mapping(string $namespacePrefix, string $path)
    {
        $this->psr4[] = ['namespacePrefix' => $namespacePrefix, 'path' => $path];
    }

    /**
     * Resolve directory path to ps-4 namespace.
     */
    public function path2Namespace(string $path)
    {
        $this->checkIfPsr4Autoloaded($path);

        $psr4Mapping = $this->findPsr4Mapping($path);

        $pathWithoutPrefix = substr($path, strlen($psr4Mapping['path']));

        return $psr4Mapping['namespacePrefix'] . '\\' . str_replace('/', '\\', $pathWithoutPrefix);
    }

    private function findPsr4Mapping(string $path): array
    {
        foreach ($this->psr4 as $item) {
            if (strpos($path, $item['path']) === 0) {
                return $item;
            }
        }
        throw new InvalidArgumentException("Could not map $path to namespace prefix");
    }

    /**
     * Check that $dir is among registered namespace prefixes
     */
    private function checkIfPsr4Autoloaded(string $path)
    {
        foreach ($this->psr4 as $item) {
            if (strpos($path, $item['path']) === 0) {
                return;
            }
        }
        throw new InvalidArgumentException("$path is not registered under psr-4 mappings");
    }
}