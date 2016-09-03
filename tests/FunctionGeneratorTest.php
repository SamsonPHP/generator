<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 12:04
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonphp\generator\FunctionGenerator;

/**
 * Class GenericGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FunctionGeneratorTest extends TestCase
{
    /** @var FunctionGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new FunctionGenerator('testFunction');
    }

    public function testDefFunction()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defLine($code)->code();
        $expected = <<<PHP
function testFunction()
{
 echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }
}
