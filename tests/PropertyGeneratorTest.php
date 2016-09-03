<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 12:04
 */
namespace samsonframework\generator\tests;

use PHPUnit\Framework\TestCase;
use samsonphp\generator\CommentsGenerator;
use samsonphp\generator\PropertyGenerator;

/**
 * Class PropertyGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class PropertyGeneratorTest extends TestCase
{
    /** @var PropertyGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new PropertyGenerator('testProperty');
    }

    public function testProperty()
    {
        $generated = $this->generator->code();

        $expected = <<<'PHP'
public $testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }
}
