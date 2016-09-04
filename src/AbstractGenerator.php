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

    /** @var array Generated code grouped by generator class name */
    protected $generatedCode = [];

    /** @var int Indentation level */
    protected $indentation = 0;

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
     * Decrease indentation.
     *
     * @return $this
     */
    public function decreaseIndentation() : AbstractGenerator
    {
        $this->indentation--;

        return $this;
    }

    /**
     * Increase indentation.
     *
     * @return $this
     */
    public function increaseIndentation() : AbstractGenerator
    {
        $this->indentation++;

        return $this;
    }

    /**
     * Close current generator and return parent.
     *
     * @return AbstractGenerator Parent
     */
    public function end() : AbstractGenerator
    {
        // Create array item
        $class = get_class($this);
        if (!array_key_exists($class, $this->parent->generatedCode)) {
            $this->parent->generatedCode[$class] = '';
        }

        // Pass generated code to parent
        $this->parent->generatedCode[$class] .= $this->code($this->indentation);

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
        return implode('', $indentation > 0 ? array_fill(0, $indentation, '    ') : []);
    }
}
