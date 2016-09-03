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
    /**
     * {@inheritdoc}
     */
    public function code($indentation = 0) : string
    {
        $innerIndentation = $this->indentation(1).' * ';

        $formattedCode = ['/**'];

        // Prepend inner indentation to code
        foreach ($this->code as $codeLine) {
            $formattedCode[] = $innerIndentation . $codeLine;
        }

        $formattedCode[] = '**/';

        return implode("\n" . $this->indentation($indentation), $formattedCode);
    }
}
