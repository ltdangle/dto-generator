<?php declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

class NameSpaceResolverTest extends TestCase
{
    public function test_it_throws_error_on_invalid_path()
    {
        $this->expectException(InvalidArgumentException::class);

        $resolver = new NameSpaceResolver();
        $resolver->addPsr4Mapping("My\\Name\\Space", 'src/');
        $resolver->addPsr4Mapping("Tests\\Space", 'tests/');
        $resolver->path2Namespace('some/path');

    }

    public function test_path2namespace()
    {
        $resolver = new NameSpaceResolver();
        $nameSpacePrefix = "My\\Name\\Space";
        $resolver->addPsr4Mapping($nameSpacePrefix, 'src/');
        $this->assertEquals("$nameSpacePrefix\\dir1\\Dir2\\Dir3", $resolver->path2Namespace('src/dir1/Dir2/Dir3'));
    }
}