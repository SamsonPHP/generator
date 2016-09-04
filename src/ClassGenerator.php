<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 09:58
 */
namespace samsonphp\generator;

/**
 * Class generator class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassGenerator extends AbstractGenerator
{
    /** OOP public visibility */
    const VISIBILITY_PUBLIC = 'public';

    /** OOP protected visibility */
    const VISIBILITY_PROTECTED = 'protected';

    /** OOP private visibility */
    const VISIBILITY_PRIVATE = 'private';

    /** @var string Class name */
    protected $className;

    /** @var string Class namespace */
    protected $namespace;

    /** @var array Collection of class uses */
    protected $uses = [];

    /** @var string Multiline file description */
    protected $fileDescription;

    /** @var array Class constants */
    protected $constants;

    /** @var array Class static properties */
    protected $staticProperties;

    /** @var array Class static methods */
    protected $staticMethods;

    /** @var array Class properties */
    protected $properties;

    /** @var array Class methods */
    protected $methods;

    /** @var bool Flag that class is abstract */
    protected $isAbstract;

    /** @var bool Flag that class is final */
    protected $isFinal;

    /**
     * ClassGenerator constructor.
     *
     * @param string           $className Class name
     * @param GenericGenerator $parent    Parent generator
     */
    public function __construct(string $className, GenericGenerator $parent = null)
    {
        $this->className = $className;

        parent::__construct($parent);
    }

    /**
     * Set class to be final.
     *
     * @return ClassGenerator
     */
    public function defFinal() : ClassGenerator
    {
        if ($this->isAbstract) {
            throw new \InvalidArgumentException('Class cannot be final as it is already abstract');
        }

        $this->isFinal = true;

        return $this;
    }

    /**
     * Set class to be abstract.
     *
     * @return ClassGenerator
     */
    public function defAbstract() : ClassGenerator
    {
        if ($this->isFinal) {
            throw new \InvalidArgumentException('Class cannot be abstract as it is already final');
        }

        $this->isAbstract = true;

        return $this;
    }

    /**
     * Set class file description.
     *
     * @param array $description Collection of class file description lines
     *
     * @return ClassGenerator
     */
    public function defDescription(array $description) : ClassGenerator
    {
        $commentsGenerator = new CommentsGenerator($this);
        foreach ($description as $line) {
            $commentsGenerator->defLine($line);
        }

        $this->fileDescription = $commentsGenerator->code();

        return $this;
    }

    /**
     * Set class namespace.
     *
     * @param string $namespace
     *
     * @return ClassGenerator
     */
    public function defNamespace(string $namespace) : ClassGenerator
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Set class use.
     *
     * @param string $use
     *
     * @return ClassGenerator
     */
    public function defUse(string $use) : ClassGenerator
    {
        $this->uses[] = $use;

        return $this;
    }

    /**
     * Set protected class property.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     *
     * @return PropertyGenerator
     */
    public function defProtectedProperty(string $name, $value) : PropertyGenerator
    {
        return $this->defProperty($name, $value)->defProtected();
    }

    /**
     * Set class property.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     *
     * @return PropertyGenerator
     */
    public function defProperty(string $name, $value) : PropertyGenerator
    {
        return new PropertyGenerator($name, $value, $this);
    }

    /**
     * Set protected static class property.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     *
     * @return PropertyGenerator
     */
    public function defProtectedStaticProperty(string $name, $value) : PropertyGenerator
    {
        return $this->defStaticProperty($name, $value)->defProtected();
    }

    /**
     * Set static class property.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     *
     * @return PropertyGenerator
     */
    public function defStaticProperty(string $name, $value) : PropertyGenerator
    {
        return $this->defProperty($name, $value)->defStatic();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function code(int $indentation = 0) : string
    {
        if ($this->namespace === null) {
            throw new \InvalidArgumentException('Class namespace should be defined');
        }

        $formattedCode = ['namespace ' . $this->namespace . ';'];

        // One empty line after namespace
        $formattedCode[] = '';

        // Add uses
        foreach ($this->uses as $use) {
            $formattedCode[] = 'use ' . $use . ';';
        }

        // One empty line after uses if we have them
        if (count($this->uses)) {
            $formattedCode[] = '';
        }

        // Add comments
        if (array_key_exists(CommentsGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[CommentsGenerator::class];
        }

        // Add previously generated code
        $formattedCode[] = $this->buildDefinition();
        $formattedCode[] = '{';

        // Prepend file description if present
        if ($this->fileDescription !== null) {
            array_unshift($formattedCode, $this->fileDescription);
        }

        // Add properties
        if (array_key_exists(PropertyGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->indentation($indentation + 1) . $this->generatedCode[PropertyGenerator::class];
        }

        $formattedCode[] = '}';

        $code = implode("\n" . $this->indentation($indentation), $formattedCode);

        return $code;
    }

    /**
     * Build function definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return ($this->isFinal ? 'final ' : '') .
        ($this->isAbstract ? 'abstract ' : '') .
        'class ' .
        $this->className;
    }
}
