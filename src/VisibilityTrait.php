<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 12:03
 */
namespace samsonphp\generator;

/**
 * Class VisibilityTrait
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
trait VisibilityTrait
{
    /** @var string Method visibility */
    protected $visibility = ClassGenerator::VISIBILITY_PUBLIC;

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
}
