<?php

declare(strict_types=1);

namespace Rector\Core\Tests\Rector\Architecture\DependencyInjection\AnnotatedPropertyInjectToConstructorInjectionRector;

use Iterator;
use Rector\Core\Rector\Architecture\DependencyInjection\AnnotatedPropertyInjectToConstructorInjectionRector;
use Rector\Core\Testing\PHPUnit\AbstractRectorTestCase;

final class AnnotatedPropertyInjectToConstructorInjectionRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    protected function getRectorClass(): string
    {
        return AnnotatedPropertyInjectToConstructorInjectionRector::class;
    }
}
