<?php

declare(strict_types=1);

namespace HM\Facturation\Domain\Event;

class LigneRetiree
{
    /**
     * @var string
     */
    private $ligneId;

    /**
     * @var
     */
    private $anciennePosition;

    /**
     * @param string $ligneId
     * @param int $anciennePosition
     */
    public function __construct(string $ligneId, int $anciennePosition)
    {
        $this->ligneId = $ligneId;
        $this->anciennePosition = $anciennePosition;
    }

    /**
     * @return string
     */
    public function getLigneId(): string
    {
        return $this->ligneId;
    }

    /**
     * @return int
     */
    public function getAnciennePosition(): int
    {
        return $this->anciennePosition;
    }
}
