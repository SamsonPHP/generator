<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:37
 */
namespace samsonphp\generator;

/**
 * Class GenericGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class GenericGenerator extends AbstractGenerator
{
    /** @var ClassGenerator[] Collection of classes */
    protected $classes = [];

    /** @var FunctionGenerator[] Collection of functions */
    protected $functions = [];

    /**
     * Set function.
     *
     * @param string $name Function
     * @param bool   $isStatic Flag that function is static
     *
     * @return FunctionGenerator New function generator instance
     */
    public function defFunction(string $name) : FunctionGenerator
    {
        return $this->functions[] = new FunctionGenerator($name, $this);
    }

    /**
     * Set class.
     *
     * @param string $name Class name
     *
     * @return ClassGenerator
     */
    public function defClass(string $name) : ClassGenerator
    {
        return $this->classes[] = new ClassGenerator($this, $name);
    }

    /**
     * Generate code.
     *
     * @param int $indentation Code level
     *
     * @return string Generated code
     */
    public function code(int $indentation = 0) : string
    {
        foreach ($this->classes as $classGenerator) {
            $this->code[] = $classGenerator->code($indentation);
        }

        foreach ($this->functions as $functionGenerator) {
            $this->code[] = $functionGenerator->code($indentation);
        }

        return implode("\n".$this->indentation($indentation), $this->code);
    }
}
