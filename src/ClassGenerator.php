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
class ClassGenerator extends GenericGenerator
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
    protected $uses;

    /** @var array Multiline class comment */
    protected $classComment;

    /** @var array Multiline file description */
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
     * @param GenericGenerator $parent Parent generator
     * @param string           $className Class name
     */
    public function __construct(GenericGenerator $parent, string $className)
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
     * @param string $description
     *
     * @return ClassGenerator
     */
    public function defDescription(string $description) : ClassGenerator
    {
        $this->fileDescription = $description;

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
        return new PropertyGenerator($name, $this);
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
}
