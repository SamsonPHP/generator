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
    /** @var string Function name */
    protected $name;

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
     * Build function definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return 'function '.$this->name.'()';
    }

    /**
     * {@inheritdoc}
     */
    public function code($indentation = 0) : string
    {
        $innerIndentation = $this->indentation(1);

        $formattedCode = [
            $this->buildDefinition(),
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
}
