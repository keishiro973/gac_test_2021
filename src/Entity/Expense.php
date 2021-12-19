<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Vehicle;

/**
 * Expense
 *
 * @ORM\Table(name="expense", uniqueConstraints={@ORM\UniqueConstraint(name="expense_number_idx", columns={"expense_number"})}, indexes={@ORM\Index(name="vehicle_id_idx", columns={"vehicle_id"})})
 * @ORM\Entity
 */
class Expense
{
    /**
     * @var int
     *
     * @ORM\Column(name="expense_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $expenseId;

    /**
     * @var string
     *
     * @ORM\Column(name="expense_number", type="string", length=64, nullable=false)
     */
    private string $expenseNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=255, nullable=false)
     */
    private string $invoiceNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private string $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="issued_on", type="datetime", nullable=false)
     */
    private DateTime $issuedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=0, nullable=false)
     */
    private string $category;

    /**
     * @var float
     *
     * @ORM\Column(name="value_te", type="decimal", precision=10, scale=3, nullable=false)
     */
    private float $valueTe;

    /**
     * @var float
     *
     * @ORM\Column(name="tax_rate", type="decimal", precision=5, scale=3, nullable=false)
     */
    private float $taxRate;

    /**
     * @var float
     *
     * @ORM\Column(name="value_ti", type="decimal", precision=10, scale=3, nullable=false)
     */
    private float $valueTi;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="Vehicle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehicle_id", referencedColumnName="vehicle_id")
     * })
     */
    private Vehicle $vehicle;

    public function getExpenseId(): ?int
    {
        return $this->expenseId;
    }

    public function getExpenseNumber(): ?string
    {
        return $this->expenseNumber;
    }

    public function setExpenseNumber(string $expenseNumber): self
    {
        $this->expenseNumber = $expenseNumber;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIssuedOn(): ?DateTime
    {
        return $this->issuedOn;
    }

    public function setIssuedOn(DateTime $issuedOn): self
    {
        $this->issuedOn = $issuedOn;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getValueTe(): ?string
    {
        return $this->valueTe;
    }

    public function setValueTe(string $valueTe): self
    {
        if (strlen($valueTe) > 10) {
            throw new \InvalidArgumentException('Valeur trop grande');
        }
        $this->valueTe = $valueTe;

        return $this;
    }

    public function getTaxRate(): ?string
    {
        return $this->taxRate;
    }

    public function setTaxRate(string $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getValueTi(): ?string
    {
        return $this->valueTi;
    }

    public function setValueTi(string $valueTi): self
    {
        if (strlen($valueTi) > 10) {
            throw new \InvalidArgumentException('Valeur trop grande');
        }
        $this->valueTi = $valueTi;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
