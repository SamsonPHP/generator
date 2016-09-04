<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:30
 */
namespace samsonphp\generator;

/**
 * Function generation class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FunctionGenerator extends AbstractGenerator
{
    use CodeTrait;
    
    /** @var string Function name */
    protected $name;

    /** @var array Collection of function arguments */
    protected $arguments = [];

    /** @var array Collection of function arguments descriptions */
    protected $argumentDescriptions = [];

    /**
     * FunctionGenerator constructor.
     *
     * @param GenericGenerator $parent Parent Generator
     * @param string           $name Function name
     */
    public function __construct(string $name, GenericGenerator $parent = null)
    {
        $this->name = $name;

        parent::__construct($parent);
    }

    /**
     * Set function argument.
     *
     * @param string      $name        Argument name
     * @param string|null $type        Argument type
     * @param string      $description Argument description
     *
     * @return FunctionGenerator
     */
    public function defArgument(string $name, string $type = null, string $description = null) : FunctionGenerator
    {
        $this->arguments[$name] = $type;
        $this->argumentDescriptions[$name] = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function code(int $indentation = 0) : string
    {
        $innerIndentation = $this->indentation(1);

        $formattedCode = [
            $this->buildDefinition() . '(' . $this->buildArguments() . ')',
            '{'
        ];
        // Prepend inner indentation to code
        foreach ($this->code as $codeLine) {
            $formattedCode[] = $innerIndentation.$codeLine;
        }
        $formattedCode[] = '}';

        $this->generatedCode .= implode("\n".$this->indentation($indentation), $formattedCode);

        return $this->generatedCode;
    }

    /**
     * Build function definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return 'function ' . $this->name;
    }

    /**
     * Build function arguments.
     *
     * @return string
     */
    protected function buildArguments() : string
    {
        $argumentsString = [];
        foreach ($this->arguments as $argumentName => $argumentType) {
            // Group name with type
            $argumentsString[] = ($argumentType !== null ? $argumentType . ' ' : '') . '$' . $argumentName;
        }

        return implode(', ', $argumentsString);
    }
}
