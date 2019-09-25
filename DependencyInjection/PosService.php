<?php

declare(strict_types=1);

namespace Mews\PosBundle\DependencyInjection;

use Mews\Pos\Pos;
use Mews\Pos\Exceptions\BankClassNullException;
use Mews\Pos\Exceptions\BankNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PosService
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var array
     */
    protected $config;

    /**
     * PosService constructor.
     * @param ParameterBagInterface|null $parameterBag
     */
    public function __construct(?ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = $parameterBag;

        if (null !== $this->parameterBag) {
            $this->config = [
                'currencies' => $this->parameterBag->get('pos.currencies'),
                'banks' => $this->parameterBag->get('pos.banks'),
            ];
        }
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config): self
    {
        $this->config = $config;
    }

    /**
     * @param array $account
     * @return Pos
     * @throws BankClassNullException
     * @throws BankNotFoundException
     */
    public function getPos(array $account): Pos
    {
        return new Pos($account, $this->config);
    }
}
