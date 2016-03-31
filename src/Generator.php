<?php
//[PHPCOMPRESSOR(remove,start)]
namespace samsonphp\generator;

class Generator
{
    /** Single quote for string value **/
    const QUOTE_SINGLE = "'";

    /** Double quote for string value **/
    const QUOTE_DOUBLE = '"';

    /** No quote for string heredoc value **/
    const QUOTE_NO = '';

    /** @var string Generated code */
    public $code = '';

    /** @var integer Level of code line tabbing for new lines */
    public $tabs = 0;

    /** @var string Current class name */
    public $class;

    /** @var int Current conditions nesting level */
    public $ifConditionLevel = 0;

    /**
     * Add simple text to current code position
     * @param string $text Text to add
     * @return self
     */
    public function text($text = '')
    {
        $this->code .= $text;

        return $this;
    }

    /**
     * Add current tabbing level to current line.
     *
     * @param string $endText Text to add after tabs
     * @param integer $tabs Amount of tabs to add
     * @param string $startText Text to add before tabs
     * @return Generator Chaining
     */
    public function tabs($endText = '', $tabs = null, $startText = '')
    {
        // Generate tabs array
        $tabs = isset($tabs) && $tabs ? array_fill(0, $tabs, "\t") : array();

        // Add necessary amount of tabs to line and append text
        $this->text($startText.implode('', $tabs) . $endText);

        return $this;
    }

    /**
     * Add new line to code.
     *
     * @param string $text Code to add to new line
     * @param integer $tabs Tabs count
     * @return self
     */
    public function newLine($text = '', $tabs = null)
    {
        // If no tabs count is specified set default tabs
        if (!isset($tabs)) {
            $tabs = $this->tabs;
        }

        return $this->tabs($text, $tabs, "\n");
    }

    /**
     * Add single line comment to code
     * @param string $text Comment text
     * @return self Chaining
     */
    public function comment($text = '')
    {
        return isset($text{0}) ? $this->newLine("// " . $text) : $this;
    }

    /**
     * Add multi-line comment. If array with one line is passed
     * we create special syntax comment in one line, usually
     * used for class variable definition in more compact form.
     *
     * @param array $lines Array of comments lines
     * @return self Chaining
     */
    public function multiComment(array $lines = array())
    {
        // If array is not empty
        if (sizeof($lines)) {
            $this->newLine("/**");

            // Multi-comment with single line
            if (sizeof($lines) === 1) {
                $this->text(' '.$lines[0].' */');
            } else { // Iterate comments lines and if comment line is not empty
                foreach ($lines as $line) {
                    if (isset($line{0})) {
                        $this->newLine(" * " . $line);
                    }
                }

                return $this->newLine(" */");
            }

        }

        return $this;
    }

    /**
     * Add one line variable definition comment.
     *
     * @param string $type Variable type
     * @param string $description Variable description
     * @param string $name Variable name
     * @return self Chaining
     */
    public function commentVar($type, $description, $name = '')
    {
        return $this->multiComment(array(
            '@var ' . trim($type) . (isset($name) ? trim($name) . ' ' : ' ') . trim($description)
        ));
    }

    /**
     * Add string value definition.
     *
     * @param string $value String value to add
     * @param string $tabs Tabs count
     * @param string $quote Type of quote
     * @return self Chaining
     */
    public function stringValue($value, $tabs = null, $quote = self::QUOTE_SINGLE)
    {
        return $this->tabs($quote . $value . $quote, $tabs);
    }

    /**
     * Add array values definition.
     *
     * @param array $items Array key-value pairs collection
     * @return self Chaining
     */
    public function arrayValue(array $items = array())
    {
        if (sizeof($items)) {
            $this->text('array(');
            $this->tabs++;

            // Iterate array items
            foreach ($items as $key => $value) {
                // Start array key definition
                $this->newLine()->stringValue($key)->text(' => ');

                // If item value is array - recursion
                if (is_array($value)) {
                    $this->arrayValue($value)->text(',');
                } else {
                    $this->stringValue($value)->text(',');
                }
            }

            $this->tabs--;
            $this->newLine(')');
        } else {
            $this->text('array()');
        }

        return $this;
    }

    /**
     * Add variable definition with array merging.
     *
     * @param string $name Variable name
     * @param array $value Array of key-value items for merging it to other array
     * @param string $arrayName Name of array to merge to, if no is specified - $name is used
     * @return self Chaining
     */
    public function defArrayMerge($name, array $value, $arrayName = null)
    {
        // If no other array is specified - set it to current
        if (!isset($arrayName)) {
            $arrayName = $name;
        }

        return $this->defvar($name, $value, ' = array_merge( ' . $arrayName . ', ', '')->text(');');
    }

    /**
     * Add variable definition.
     *
     * @param string $name Variable name
     * @param mixed $value Variable default value
     * @param string $after String to insert after variable definition
     * @param string $end Closing part of variable definition
     * @param string $quote Type of quote
     * @return Generator Chaining
     */
    public function defVar($name, $value = null, $after = ' = ', $end = ';', $quote = self::QUOTE_SINGLE)
    {
        // Output variable definition
        $this->newLine($name);

        // Get variable type
        switch (gettype($value)) {
            case 'integer':
            case 'boolean':
            case 'double':
                $this->text($after)->text($value)->text($end);
                break;
            case 'string':
                if (strpos($value, 'EOT') !== false) {
                    $this->text($after)->stringValue($value, 0, self::QUOTE_NO)->text($end);
                } else {
                    $this->text($after)->stringValue($value, 0, $quote)->text($end);
                }
                break;
            case 'array':
                $this->text($after)->arrayValue($value)->text($end);
                break;
            case 'NULL':
            case 'object':
            case 'resource':
            default:
                $this->text(';');
        }

        return $this;
    }

    /**
     * Add trait definition.
     *
     * @param string $name Trait name
     * @return self Chaining
     */
    public function defTrait($name)
    {
        // If we define another class, and we were in other class context
        if (isset($this->class) && ($name !== $this->class)) {
            // Close old class context
            $this->endClass();
        }

        // Save new class name
        $this->class = $name;

        $this->newLine('{');

        $this->tabs++;

        return $this;
    }

    /**
     * Add class definition.
     *
     * @param string $name Class name
     * @param string $extends Parent class name
     * @param array $implements Interfaces names collection
     * @return self Chaining
     */
    public function defClass($name, $extends = null, array $implements = array())
    {
        // If we define another class, and we were in other class context
        if (isset($this->class) && ($name !== $this->class)) {
            // Close old class context
            $this->endClass();
        }

        // Save new class name
        $this->class = $name;

        // Class definition start
        $this->newLine('class ' . $name);

        // Parent class definition
        if (isset($extends)) {
            $this->text(' extends ' . $extends);
        }

        // Interfaces
        if (sizeof($implements)) {
            $this->text(' implements ' . implode(',', $implements));
        }

        $this->newLine('{');

        $this->tabs++;

        return $this;
    }

    /**
     * Close current class context.
     *
     * @return self Chaining
     */
    public function endClass()
    {
        $this->tabs--;

        // Close class definition
        return $this->newLine('}')
            // Add one empty line after class definition
        ->newLine('');
    }

    /**
     * Define if statement condition.
     *
     * @param string $condition Condition statement
     * @return self Chaining
     */
    public function defIfCondition($condition)
    {
        $this->ifConditionLevel++;

        // Class definition start
        $this->newLine('if (' . $condition . ') {');
        $this->tabs++;
        return $this;
    }

    /**
     * Define elseif statement condition.
     *
     * @param string $condition Condition statement
     * @return self Chaining
     */
    public function defElseIfCondition($condition)
    {
        $this->tabs--;
        // Class definition start
        $this->newLine('} elseif (' . $condition . ') {');
        $this->tabs++;
        return $this;
    }

    /**
     * Define else statement.
     *
     * @return self Chaining
     */
    public function defElseCondition()
    {
        $this->tabs--;
        // Class definition start
        $this->newLine('} else {');
        $this->tabs++;
        return $this;
    }

    /**
     * Close if condition statement.
     *
     * @return self Chaining
     */
    public function endIfCondition()
    {
        if ($this->ifConditionLevel--) {
            $this->tabs--;

            // Close class definition
            return $this->newLine('}');
        }

        return $this;
    }

    /**
     * Add class variable definition.
     *
     * @param string $name Variable name
     * @param string $visibility Variable accessibility level
     * @param mixed $value Variable default value
     * @return self Chaining
     */
    public function defClassVar($name, $visibility = 'public', $value = null)
    {
        if (isset($comment) && isset($comment{0})) {
            $this->multiComment(array($comment));
        }

        return $this->defvar($visibility . ' ' . $name, $value)->newLine();
    }

    /**
     * Add class constant definition.
     *
     * @param string $name Constant name
     * @param string $value Variable default value
     * @return self Chaining
     */
    public function defClassConst($name, $value)
    {
        return $this->defClassVar(strtoupper($name), 'const', $value);
    }

    /**
     * Write file to disk
     * @param string $name Path to file
     * @param string $format Output file format
     */
    public function write($name, $format = 'php')
    {
        $code = $this->flush();

        if ($format === 'php') {
            $code = '<?php ' . $code;
        }

        file_put_contents($name, $code, 0775);
    }

    /**
     * Flush internal data and return it.
     *
     * @return string Current generated code
     */
    public function flush()
    {
        // We should use 4 spaces instead of tabs
        $code = str_replace("\t", '    ', $this->code);

        $this->tabs = 0;
        $this->code = '';
        $this->class = null;

        return $code;
    }

    /**
     * Add function definition.
     *
     * @param string $name Function name
     * @param array $parameters Collection of parameters $typeHint => $paramName
     * @return Generator Chaining
     */
    public function defFunction($name, $parameters = array())
    {
        // Convert parameters to string
        $parameterList = array();
        foreach ($parameters as $type => $parameter) {
            $parameterList[] = (is_string($type) ? $type.' ' : '') . $parameter;
        }
        $parameterList = sizeof($parameterList) ? implode(', ', $parameterList) : '';

        $this->newLine('function ' . $name . '('.$parameterList.')')
            ->newLine('{')
            ->tabs('');

        $this->tabs++;

        return $this;
    }

    /**
     * Close current function context.
     *
     * @return self Chaining
     */
    public function endFunction()
    {
        $this->tabs--;

        return $this->newLine('}')->newLine('');
    }

    /**
     * Constructor
     * @param string $namespace Code namespace
     */
    public function __construct($namespace = null)
    {
        // If namespace is defined - set it
        if (isset($namespace)) {
            $this->defnamespace($namespace);
        }
    }

    /**
     * Add namespace declaration
     * @param string $name Namespace name
     * @return self
     */
    public function defNamespace($name)
    {
        return $this->newLine('namespace ' . $name . ';')->newLine();
    }
}
//[PHPCOMPRESSOR(remove,end)]
