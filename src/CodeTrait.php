<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 10:13
 */
namespace samsonphp\generator;

/**
 * Trait for generators that can generate internal code.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
trait CodeTrait
{
    /** @var array Collection of code lines */
    protected $code = [];

    /**
     * Add function code line.
     *
     * @param string $code Code line
     *
     * @return $this
     */
    public function defLine(string $code)
    {
        $this->code[] = $code;

        return $this;
    }
}
