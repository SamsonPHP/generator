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

    /** @var string Generated code */
    protected $generatedCode;

    /** @var int Indentation level */
    protected $indentation = 0;

    /** @var array Collection of code lines */
    protected $code = [];

    /**
     * MethodGenerator constructor.
     *
     * @param GenericGenerator $parent Parent generator
     */
    public function __construct(AbstractGenerator $parent = null)
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
        // Pass generated code to parent
        $this->parent->generatedCode .= $this->code($this->indentation)."\n";

        return $this->parent;
    }

    /**
     * Generate code.
     *
     * @param int $indentation Code level
     *
     * @return string Generated code
     */
    abstract public function code(int $indentation = 0) : string;

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
     * Set Comments block.
     *
     * @return CommentsGenerator Comments block generator
     */
    public function defComment() : CommentsGenerator
    {
        return new CommentsGenerator($this);
    }

    /**
     * Get indentation string.
     *
     * @param int $indentation Code level
     *
     * @return string Indentation string
     */
    protected function indentation(int $indentation = 0) : string
    {
        return implode('', $indentation > 0 ? array_fill(0, $indentation, ' ') : []);
    }
}
