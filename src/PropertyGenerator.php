<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:30
 */
namespace samsonphp\generator;

/**
 * Class property generation class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class PropertyGenerator extends AbstractGenerator
{
    /** @var string Property name */
    protected $name;

    /** @var bool Flag that method is static */
    protected $isStatic = false;

    /** @var string Method visibility */
    protected $visibility = ClassGenerator::VISIBILITY_PUBLIC;

    /**
     * PropertyGenerator constructor.
     *
     * @param string                 $name Property name
     * @param AbstractGenerator|null $parent Parent generator
     */
    public function __construct(string $name, AbstractGenerator $parent = null)
    {
        $this->name = $name;

        parent::__construct($parent);
    }

    /**
     * Set property type hint comment
     *
     * @param string      $type        Property type
     * @param string|null $description Property description
     *
     * @return PropertyGenerator
     */
    public function defTypeHint(string $type, string $description = null) : PropertyGenerator
    {
        return (new CommentsGenerator($this))
            ->defLine('@var ' . $type . ($description ?? ''))
            ->end();
    }

    /**
     * Set protected property visibility.
     *
     * @return PropertyGenerator
     */
    public function defProtected() : PropertyGenerator
    {
        return $this->defVisibility(ClassGenerator::VISIBILITY_PROTECTED);
    }

    /**
     * Set property visibility.
     *
     * @param string $visibility Property visibility
     *
     * @return PropertyGenerator
     */
    protected function defVisibility(string $visibility) : PropertyGenerator
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Set private property visibility.
     *
     * @return PropertyGenerator
     */
    public function defPrivate() : PropertyGenerator
    {
        return $this->defVisibility(ClassGenerator::VISIBILITY_PRIVATE);
    }

    /**
     * Set method to be static.
     *
     * @return PropertyGenerator
     */
    public function defStatic() : PropertyGenerator
    {
        $this->isStatic = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function code($indentation = 0) : string
    {
        $this->generatedCode .= $this->indentation($indentation)
            .$this->visibility
            .' '
            .($this->isStatic ? 'static ' : '')
            .'$'
            .$this->name
            .';';

        return $this->generatedCode;
    }
}
