<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:37
 */
namespace samsonphp\generator;

/**
 * Abstract code generator.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
abstract class AbstractGenerator
{
    /** @var GenericGenerator Parent class generator */
    protected $parent;

    /** @var array Collection of code lines */
    protected $code = [];

    /**
     * MethodGenerator constructor.
     *
     * @param GenericGenerator $parent Parent generator
     */
    public function __construct(GenericGenerator $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Close current generator and return parent.
     *
     * @return AbstractGenerator Parent
     */
    public function end() : AbstractGenerator
    {
        return $this->parent;
    }

    /**
     * Get indentation string.
     *
     * @param int $indentation Code level
     *
     * @return string Indentation string
     */
    protected function indentation($indentation = 0) : string
    {
        return implode('', $indentation > 0 ? array_fill(0, $indentation, ' ') : []);
    }

    /**
     * Add function code line.
     *
     * @param string $code Code line
     *
     * @return AbstractGenerator
     */
    public function defLine(string $code) : AbstractGenerator
    {
        $this->code[] = $code;

        return $this;
    }

    /**
     * Generate code.
     *
     * @param int $indentation Code level
     *
     * @return string Generated code
     */
    abstract public function code($indentation = 0) : string;
}
