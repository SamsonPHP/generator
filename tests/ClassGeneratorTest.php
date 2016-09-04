<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 10:49
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonphp\generator\ClassGenerator;

/**
 * Class ClassGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassGeneratorTest extends TestCase
{
    /** @var ClassGenerator */
    protected $classGenerator;

    public function setUp()
    {
        $this->classGenerator = new ClassGenerator('testClass');
    }

    public function testDefNamespace()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefDescription()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defDescription(['File description'])
            ->code();

        $expected = <<<'PHP'
/** File description */
namespace testname\space;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }
}
