<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GasStation
 *
 * @ORM\Table(name="gas_station", uniqueConstraints={@ORM\UniqueConstraint(name="expense_id", columns={"expense_id"})})
 * @ORM\Entity
 */
class GasStation
{
    /**
     * @var int
     *
     * @ORM\Column(name="gas_station_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $gasStationId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="coordinate", type="string", length=0, nullable=false)
     */
    private $coordinate;

    /**
     * @var Expense
     *
     * @ORM\ManyToOne(targetEntity="Expense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="expense_id", referencedColumnName="expense_id")
     * })
     */
    private $expense;

    public function getGasStationId(): ?int
    {
        return $this->gasStationId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCoordinate(): ?string
    {
        return $this->coordinate;
    }

    public function setCoordinate(string $coordinate): self
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    public function setExpense(?Expense $expense): self
    {
        $this->expense = $expense;

        return $this;
    }


}
