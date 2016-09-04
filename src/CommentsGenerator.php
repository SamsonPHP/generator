<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 13:02
 */
namespace samsonphp\generator;

/**
 * Comments block generator.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class CommentsGenerator extends AbstractGenerator
{
    use CodeTrait;

    /**
     * Set var comment line.
     *
     * @param string      $type        Type
     * @param string|null $description Description
     *
     * @return CommentsGenerator
     */
    public function defVar(string $type, string $description = null) : CommentsGenerator
    {
        return $this->defLine('@var ' . $type . ($description !== null ? ' ' . $description : ''));
    }

    /**
     * Set param comment line.
     *
     * @param string      $name        Argument name
     * @param string      $type        Argument type
     * @param string|null $description Argument description
     *
     * @return CommentsGenerator
     */
    public function defParam(string $name, string $type, string $description = null) : CommentsGenerator
    {
        return $this->defLine('@param ' . $type . ' ' . $name . ($description !== null ? ' ' . $description : ''));
    }

    /**
     * {@inheritdoc}
     */
    public function code(int $indentation = 0) : string
    {
        $indentationString = $this->indentation($indentation);

        return count($this->code) === 1
            ? $this->formatSingleLine($indentationString)
            : $this->formatMultiLine($indentationString);
    }

    /**
     * Format comments code into single line comment.
     *
     * @param string $indentation Indentation string
     *
     * @return string Single line comments code
     */
    protected function formatSingleLine(string $indentation)
    {
        return $indentation.'/** '.$this->code[0].' */';
    }

    /**
     * Format comments code into multi line comment.
     *
     * @param string $indentation Indentation string
     *
     * @return string Multi-line comments code
     */
    protected function formatMultiLine(string $indentation)
    {
        $formattedCode = ['/**'];

        // Prepend inner indentation to code
        foreach ($this->code as $codeLine) {
            $formattedCode[] = ' * ' . $codeLine;
        }

        $formattedCode[] = ' */';

        return implode("\n" . $indentation, $formattedCode);
    }
}
