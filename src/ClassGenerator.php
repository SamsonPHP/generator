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
    use AbstractFinalTrait;

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

    /** @var array Collection of class used traits */
    protected $traits = [];

    /** @var string Multi-line file description */
    protected $fileDescription;

    /** @var array Class methods */
    protected $methods;

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
     * @param string $use Use class name
     *
     * @return ClassGenerator
     */
    public function defUse(string $use) : ClassGenerator
    {
        $this->uses[] = $use;

        return $this;
    }

    /**
     * Set class trait use.
     *
     * @param string $trait Trait class name
     *
     * @return ClassGenerator
     */
    public function defTrait(string $trait) : ClassGenerator
    {
        $this->traits[] = $trait;

        return $this;
    }

    /**
     * Set protected class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defProtectedProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return $this->defProperty($name, $type, $value, $description)->defProtected();
    }

    /**
     * Set class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return (new PropertyGenerator($name, $value, $this))
            ->increaseIndentation()
            ->defComment()
            ->defVar($type, $description)
            ->end();
    }

    /**
     * Set protected static class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defProtectedStaticProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return $this->defStaticProperty($name, $type, $value, $description)->defProtected();
    }

    /**
     * Set static class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defStaticProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return $this->defProperty($name, $type, $value, $description)->defStatic();
    }

    /**
     * Set protected class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defProtectedMethod(string $name) : MethodGenerator
    {
        return $this->defMethod($name)->defProtected();
    }

    /**
     * Set public class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defMethod(string $name) : MethodGenerator
    {
        return (new MethodGenerator($name, $this))->increaseIndentation();
    }

    /**
     * Set protected static class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defProtectedStaticMethod(string $name) : MethodGenerator
    {
        return $this->defStaticMethod($name)->defProtected();
    }

    /**
     * Set public static class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defStaticMethod(string $name) : MethodGenerator
    {
        return $this->defMethod($name)->defStatic();
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

        $indentationString = $this->indentation($indentation);
        $innerIndentation = $this->indentation(1);

        // Add traits
        foreach ($this->traits as $trait) {
            $formattedCode[] = $innerIndentation . 'use ' . $trait . ';';
        }

        // One empty line after traits if we have them
        if (count($this->traits)) {
            $formattedCode[] = '';
        }

        // Prepend file description if present
        if ($this->fileDescription !== null) {
            array_unshift($formattedCode, $this->fileDescription);
        }

        // Add properties
        if (array_key_exists(PropertyGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[PropertyGenerator::class];
        }

        // Add properties
        if (array_key_exists(MethodGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[MethodGenerator::class];
        }

        $formattedCode[] = '}';

        $code = implode("\n" . $indentationString, $formattedCode);

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
