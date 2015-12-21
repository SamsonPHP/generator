<?php
//[PHPCOMPRESSOR(remove,start)]
namespace samsonphp\generator;

class Generator
{

	/** Single quote for string value **/
	const QUOTE_SINGLE = "'";
	
	/** Double quote for string value **/
	const QUOTE_DOUBLE = '"';

	/** Generated code */
	public $code = '';

	/** Level of code line tabbing for new lines */
	public $tabs = 0;

	/** Current classname */
	public $class;

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
	 * Add current tabbing level to current line
	 * @param string $text Text to add after tabs
	 * @param integer $tabs Amount of tabs to add
	 * @return self Chaining
	 */
	public function tabs($text = '', $tabs = null)
	{
		// Generate tabs array
		$_tabs = $tabs ? array_fill(0, $tabs, "\t") : array();

		// Add nessar amount of tabs to line and append text
		$this->text(implode('', $_tabs) . $text);

		return $this;
	}

	/**
	 * Add new line to code
	 * @param string $code Code to add to new line
	 * @param integer $tabs Tabs count
	 * @return self
	 */
	public function newline($text = '', $tabs = null)
	{
		// If no tabs count is specified set default tabs
		if (!isset($tabs)) $tabs = $this->tabs;

		return $this->tabs("\n" . $text, $tabs);
	}

	/**
	 * Add single line comment to code
	 * @param string $text Comment text
	 * @return self Chaining
	 */
	public function comment($text = '')
	{
		return isset($text{0}) ? $this->newline("// " . $text) : $this;
	}

	/**
	 * Add multiline comment
	 * @param array $lines Array of comments lines
	 * @return self Chaining
	 */
	public function multicomment(array $lines = array())
	{
		// If array is not empty
		if (sizeof($lines)) {
			$this->newline("/**");

			// Iterate comments lines and if comment line is not empty
			foreach ($lines as $line) if (isset($line{0})) $this->newline(" * " . $line);

			return $this->newline(" */");
		} else return $this;
	}

	/**
	 * Add string value definition
	 * @param string $value String value to add
	 * @param string $tabs Tabs count
	 * @param string $quote Type of quote
	 * @return self
	 */
	public function stringvalue($value, $tabs = null, $quote = self::QUOTE_SINGLE)
	{
		return $this->tabs($quote . $value . $quote, $tabs);
	}

	/**
	 * Add array values definition
	 * @param array $items Array key-value pairs collection
	 * @return self Chaining
	 */
	public function arrayvalue(array $items = array())
	{
		$this->text('array(');
		$this->tabs++;

		/*
		// TODO: Add array key-value выравнивание
		 * 
		// Determine largest key
		// Convert array to array of key lengths
		$lengths = array_map('strlen', array_keys($items));
		// Get lergest element
        $maxLength = max( $lengths );
        // Find largest position
        $key = array_search( $maxLength, $lengths );        
        $tab_size = 3;
        $tabs = round($maxLength / $tab_size);
        */

		// Iterate array items
		foreach ($items as $key => $value) {
			// Start array key definition
			$this->newline()->stringvalue($key, 2)->tabs('=>', 1);

			// If item value is array - recursion
			if (is_array($value)) $this->arrayvalue($value)->text(',');
			// Output value
			else $this->stringvalue($value, 1)->text(',');
		}

		$this->newline(')');
		$this->tabs--;

		return $this;
	}

	/**
	 * Add variable definition with arraymerging
	 * @param string $name Variable name
	 * @param array $value Array of key-value items for merging it to other array
	 * @param string $arrayname Name of array to merge to, if no is specified - $name is used
	 * @return self Chaining
	 */
	public function defarraymerge($name, array $value, $arrayname = null)
	{
		// If no other array is specified - set it to current
		if (!isset($arrayname)) $arrayname = $name;

		return $this->defvar($name, $value, ' = array_merge( ' . $arrayname . ', ', '')->text(');');
	}

	/**
	 * Add variable definition
	 * @param string $name Variable name
	 * @param string $value Variable default value
	 * @param string $after String to insert after variable definition
	 * @param string $quote Type of quote
	 * @return self Chaining
	 */
	public function defvar($name, $value = null, $after = ' = ', $end = ';', $quote = self::QUOTE_SINGLE)
	{
		// Output variable definition
		$this->newline($name);

		// Get variable type
		switch (gettype($value)) {
			case 'null'    :
				$this->text(';');
				break;
			case 'integer'    :
			case 'double'    :
				$this->text($after, 1)->text($value)->text($end);
				break;
			case 'string'    :
				$this->text($after, 1)->stringvalue($value, 1, $quote)->text($end);
				break;
			case 'array'    :
				$this->text($after, 1)->arrayvalue($value, 1)->text($end);
				break;
		}

		return $this;
	}

	/**
	 * Add class definition
	 * @param string $name Class name
	 * @param string $extends Parent class name
	 * @param array $implements Interfaces names collection
	 * @return self Chaining
	 */
	public function defclass($name, $extends = null, array $implements = array())
	{
		// If we define another class, and we were in other class context
		if (isset($this->class) && ($name !== $this->class)) {
			// Close old class context 
			$this->endclass();
		}

		// Save new classname
		$this->class = $name;

		// Class definition start
		$this->newline('class ' . $name);

		// Parent class definition
		if (isset($extends)) $this->text(' extends ' . $extends);

		// Interfaces
		if (sizeof($implements)) $this->text(' implements ' . implode(',', $implements));

		return $this->tabs('{', 1);
	}

	/**
	 * Close current class context
	 * @return\samson\activerecord\Generator
	 */
	public function endclass()
	{
		return $this->newline('}')->newline('');
	}

	/**
	 * Add class variable definition
	 * @param string $name Variable name
	 * @param string $value Variable default value
	 * @param string $visibility Variable accesebility level
	 * @return self Chaining
	 */
	public function defclassvar($name, $value = null, $visibility = 'public')
	{
		return $this->defvar($visibility . ' ' . $name, $value);
	}

	/**
	 * Write file to disk
	 * @param string $name Path to file
	 * @param string $format Output file format
	 */
	public function write($name, $format = 'php')
	{
		$code = $this->code;

		if ($format == 'php') $code = '<?php ' . $code;

		file_put_contents($name, $code, 0775);
	}

	/**
	 * Flush internal data and return it.
	 *
	 * @return string Current generated code
	 */
	public function flush()
	{
		$code = $this->code;

		$this->tabs = 0;
		$this->code = '';
		$this->class = null;

		return $code;
	}

	/**
	 * Add function definition
	 * @param string $name Function name
	 * @return self Chaining
	 */
	public function deffunction($name)
	{
		return $this->newline('function ' . $name . '()')
			->newline('{')
			->tabs('', 1);
	}

	/**
	 * Close current function context
	 * @return\samson\activerecord\Generator
	 */
	public function endfunction()
	{
		return $this->newline('}')->newline('');
	}

	/**
	 * Constructor
	 * @param string $namespace Code namespace
	 */
	public function __construct($namespace = null)
	{
		// If namespace is defined - set it
		if (isset($namespace)) $this->defnamespace($namespace);
	}

	/**
	 * Add namespace declaration
	 * @param string $name Namespace name
	 * @return self
	 */
	private function defnamespace($name)
	{
		return $this->newline('namespace ' . $name . ';')->newline();
	}

}
//[PHPCOMPRESSOR(remove,end)]
