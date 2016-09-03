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
    protected function formatSingleLine(string $indentation)
    {
        return $indentation.'/** '.$this->code[0].' */';
    }

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

    /**
     * {@inheritdoc}
     */
    public function code($indentation = 0) : string
    {
        $indentationString = $this->indentation($indentation);

        return count($this->code) === 1
            ? $this->formatSingleLine($indentationString)
            : $this->formatMultiLine($indentationString);
    }
}
