<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

class NameSpaceResolverTest extends TestCase
{
    public function testItThrowsErrorOnInvalidPath()
    {
        $this->expectException(InvalidArgumentException::class);
        NameSpaceResolver::validatePath('src');
    }

    public function testItThrowsErrorOnUnconfiguredPath(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $resolver = new NameSpaceResolver();
        $resolver->addPsr4Mapping('My\\Name\\Space\\', 'src/');
        $resolver->addPsr4Mapping('Tests\\Space\\', 'tests/');
        $resolver->path2Namespace('some/path');
    }

    public function testDeepNamespace(): void
    {
        $resolver = new NameSpaceResolver();
        $resolver->addPsr4Mapping('My\\Name\\Space\\', 'src/');
        $this->assertEquals('My\\Name\\Space\\dir1\\Dir2\\Dir3', $resolver->path2Namespace('src/dir1/Dir2/Dir3'));
    }

    public function testTopLevelNamespace(): void
    {
        $resolver = new NameSpaceResolver();
        $resolver->addPsr4Mapping('My\\Name\\Space\\', 'src/');
        $this->assertEquals('My\\Name\\Space', $resolver->path2Namespace('src/'));
    }
}
