<?php declare(strict_types=1);
//[PHPCOMPRESSOR(remove,start)]
namespace samsonphp\generator;

/**
 * PHP code generator.
 * @deprecated Use other separate generators.
 * @author     Vitaly Egorov <egorov@samsonos.com>
 */
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
     * Constructor
     *
     * @param string $namespace Code namespace
     *
     * @deprecated Use new generators logic
     */
    public function __construct($namespace = null)
    {
        // If namespace is defined - set it
        if (isset($namespace)) {
            $this->defNamespace($namespace);
        }
    }

    /**
     * Add namespace declaration.
     *
     * @param string $name Namespace name
     *
     * @deprecated Use new generators logic
     * @return $this Chaining
     */
    public function defNamespace($name)
    {
        if ($name !== '' && $name !== null) {
            $this->newLine('namespace ' . $name . ';')->newLine();
        }

        return $this;
    }
    
    /**
     * Add new line to code.
     *
     * @param string $text Code to add to new line
     * @param integer $tabs Tabs count
     * 
*@return self
     * @deprecated Use new generators logic
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
     * Add current tabbing level to current line.
     *
     * @param string  $endText   Text to add after tabs
     * @param integer $tabs      Amount of tabs to add
     * @param string  $startText Text to add before tabs
     *
     * @return Generator Chaining
     * @deprecated Use new generators logic
     */
    public function tabs($endText = '', $tabs = null, $startText = '')
    {
        // Generate tabs array
        $tabs = isset($tabs) && $tabs > 0 ? array_fill(0, $tabs, "\t") : array();

        // Add necessary amount of tabs to line and append text
        $this->text($startText . implode('', $tabs) . $endText);

        return $this;
    }

    /**
     * Add simple text to current code position
     *
     * @param string $text Text to add
     *
     * @return self
     * @deprecated Use new generators logic
     */
    public function text($text = '')
    {
        $this->code .= $text;

        return $this;
    }

    /**
     * Increase current code indentation.
     *
     * @param int $amount Indentation amount
     *
     * @deprecated Use new generators logic
     *
     * @return $this Chaining
     */
    public function increaseIndentation($amount = 1)
    {
        $this->tabs += $amount;

        return $this;
    }

    /**
     * Reduce current code indentation.
     *
     * @param int $amount Indentation amount
     *
     * @deprecated Use new generators logic
     *
     * @return $this Chaining
     */
    public function decreaseIndentation($amount = 1)
    {
        $this->tabs = $this->tabs > $amount ? $this->tabs - $amount : 0;

        return $this;
    }

    /**
     * Add single line comment to code
     *
     * @param string $text Comment text
     *
     * @return self Chaining
     * @deprecated Use new generators logic
     */
    public function comment($text = '')
    {
        return isset($text{0}) ? $this->newLine("// " . $text) : $this;
    }

    /**
     * Add one line variable definition comment.
     *
     * @param string $type Variable typeHint
     * @param string $description Variable description
     * @param string $name Variable name
     * 
*@return self Chaining
     * @deprecated Use new generators logic
     */
    public function commentVar($type, $description, $name = '')
    {
        return $this->multiComment(array(
            '@var ' . trim($type) . (isset($name) ? trim($name) . ' ' : ' ') . trim($description)
        ));
    }

    /**
     * Add multi-line comment. If array with one line is passed
     * we create special syntax comment in one line, usually
     * used for class variable definition in more compact form.
     *
     * @param array $lines Array of comments lines
     *
     * @deprecated Use new generators logic
     * @return self Chaining
     */
    public function multiComment(array $lines = array())
    {
        // If array is not empty
        if (sizeof($lines)) {
            $this->newLine("/**");

            // Multi-comment with single line
            if (sizeof($lines) === 1) {
                $this->text(' ' . $lines[0] . ' */');
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
     * Add variable definition with array merging.
     *
     * @param string $name Variable name
     * @param array $value Array of key-value items for merging it to other array
     * @param string $arrayName Name of array to merge to, if no is specified - $name is used
     * 
*@return self Chaining
     * @deprecated Use new generators logic
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
     * 
*@return Generator Chaining
     * @deprecated Use new generators logic
     */
    public function defVar($name, $value = null, $after = ' = ', $end = ';', $quote = self::QUOTE_SINGLE)
    {
        // Output variable definition
        $this->newLine($name);

        // Get variable typeHint
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
     * Add string value definition.
     *
     * @param string $value String value to add
     * @param string $tabs  Tabs count
     * @param string $quote Type of quote
     *
     * @deprecated Use new generators logic
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
     *
     * @deprecated Use new generators logic
     * @return self Chaining
     */
    public function arrayValue(array $items = array())
    {
        if (sizeof($items)) {
            $this->text('[');
            $this->tabs++;

            // Iterate array items
            foreach ($items as $key => $value) {
                // Start array key definition
                $this->newLine()->defineValue($key)->text(' => ')->defineValue($value)->text(',');
            }

            $this->tabs--;
            $this->newLine(']');
        } else {
            $this->text('[]');
        }

        return $this;
    }

    /**
     * Generate correct value.
     *
     * Metho handles arrays, numerics, strings and constants.
     *
     * @param mixed $value Value to put in generated code
     *
     * @deprecated Use new generators logic
     * @return $this
     */
    protected function defineValue($value)
    {
        // If item value is array - recursion
        if (is_array($value)) {
            $this->arrayValue($value);
        } elseif (is_numeric($value) || is_float($value)) {
            $this->text($value);
        } else {
            try { // Try to evaluate
                eval('$value2 = ' . $value . ';');
                $this->text($value);
            } catch (\Throwable $e) { // Consider it as a string
                $this->stringValue($value);
            }
        }

        return $this;
    }

    /**
     * Add trait definition.
     *
     * @param string $name Trait name
     *
     * @return self Chaining
     * @deprecated Use new generators logic
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

        // Class definition start
        $this->newLine('trait ' . $name);

        $this->newLine('{');

        $this->tabs++;

        return $this;
    }

    /**
     * Close current class context.
     *
     * @return self Chaining
     * @deprecated Use new generators logic
     */
    public function endClass()
    {
        $this->tabs > 0 ? $this->tabs-- : null;

        // Close class definition
        return $this->newLine('}')
            // Add one empty line after class definition
            ->newLine('');
    }

    /**
     * Add class definition.
     *
     * @param string $name Class name
     * @param string $extends Parent class name
     * @param array $implements Interfaces names collection
     *
     * @return self Chaining
     * @deprecated Use new generators logic
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
     * Define if statement condition.
     *
     * @param string $condition Condition statement
     *
     * @return self Chaining
     * @deprecated Use new generators logic
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
     *
     * @return self Chaining
     * @deprecated Use new generators logic
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
     * @deprecated Use new generators logic
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
     * @deprecated Use new generators logic
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
     * Add class constant definition.
     *
     * @param string $name  Constant name
     * @param string $value Variable default value
     *
     * @return self Chaining
     * @deprecated Use new generators logic
     */
    public function defClassConst($name, $value)
    {
        return $this->defClassVar(strtoupper($name), 'const', $value);
    }

    /**
     * Add class variable definition.
     *
     * @param string $name Variable name
     * @param string $visibility Variable accessibility level
     * @param mixed $value Variable default value
     *
     * @return self Chaining
     * @deprecated Use new generators logic
     */
    public function defClassVar($name, $visibility = 'public', $value = null)
    {
        if (isset($comment) && isset($comment{0})) {
            $this->multiComment(array($comment));
        }

        return $this->defvar($visibility . ' ' . $name, $value)->newLine();
    }

    /**
     * Write file to disk
     * 
*@param string       $name   Path to file
     * @param string $format Output file format
     *
     *@deprecated Use new generators logic
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
     * @deprecated Use new generators logic
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
     * @see self::defClassFunction with public visibility
     *
     * @return $this
     * @deprecated Use new generators logic
     */
    public function defPublicClassFunction(string $name, array $parameters = [], array $comments = [], $returnType = null)
    {
        return $this->defClassFunction($name, 'public', $parameters, $comments, $returnType);
    }

    /**
     * Add class function definition.
     *
     * @param string $name       Class function name
     * @param string $visibility Class function visibility
     * @param array  $parameters Class function arguments
     * @param array  $comments   Class function multi-line comments
     * @param null   $returnType Class function return type PHP7
     *
     * @return $this
     * @deprecated Use new generators logic
     */
    public function defClassFunction(string $name, string $visibility = 'public', array $parameters = [], array $comments = [], $returnType = null)
    {
        if ($this->class === null) {
            throw new \InvalidArgumentException('Cannot create class function '.$name.' with out class creation');
        }

        $this->defFunction($name, $parameters, $visibility.' ', $comments, $returnType);

        return $this;
    }

    /**
     * Add function definition.
     *
     * @param string $name       Function name
     * @param array  $parameters Collection of parameters $typeHint => $paramName
     * @param string $prefix     Function prefix
     * @param array  $comments   Function multi-line comments
     * @param string $returnType Function return type PHP7
     *
     * @return Generator Chaining
     * @deprecated Use new generators logic
     */
    public function defFunction(string $name, array $parameters = [], string $prefix = '', array $comments = [], string $returnType = null)
    {
        // Convert parameters to string
        $parameterList = array();
        foreach ($parameters as $type => $parameter) {
            $parameterList[] = (is_string($type) ? $type . ' ' : '') . $parameter;
        }
        $parameterList = sizeof($parameterList) ? implode(', ', $parameterList) : '';

        $this
            ->newLine('')
            ->multiComment($comments)
            ->newLine($prefix . 'function ' . $name . '(' . $parameterList . ')' . ($returnType !== null ? ' : ' . $returnType : ''))
            ->newLine('{')
            ->tabs('');

        $this->tabs++;

        return $this;
    }

    /**
     * @see self::defClassFunction with private visibility
     *
     * @return $this
     * @deprecated Use new generators logic
     */
    public function defPrivateClassFunction(string $name, array $parameters = [], array $comments = [], $returnType = null)
    {
        return $this->defClassFunction($name, 'private', $parameters, $comments, $returnType);
    }

    /**
     * @see self::defClassFunction with protected visibility
     * @deprecated Use new generators logic
     * @return $this
     */
    public function defProtectedClassFunction(string $name, array $parameters = [], array $comments = [], $returnType = null)
    {
        return $this->defClassFunction($name, 'protected', $parameters, $comments, $returnType);
    }

    /**
     * Close class function definition.
     * @deprecated Use new generators logic
     * @return $this Chaining
     */
    public function endClassFunction()
    {
        $this->endFunction();

        return $this;
    }

    /**
     * Close current function context.
     * @deprecated Use new generators logic
     * @return self Chaining
     */
    public function endFunction()
    {
        $this->tabs--;

        return $this->newLine('}');
    }
}
//[PHPCOMPRESSOR(remove,end)]
