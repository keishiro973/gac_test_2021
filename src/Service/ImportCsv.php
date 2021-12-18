<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\GasStation;
use App\Entity\Vehicle;
use App\Repository\ExpenseRepository;
use App\Repository\GasStationRepository;
use App\Repository\VehicleRepository;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnableToProcessCsv;

class ImportCsv
{
    const limiteFlush = 20;

    private EntityManagerInterface $em;
    private VehicleRepository $vehicleRepository;
    private ExpenseRepository $expenseRepository;
    private GasStationRepository $gasStationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        VehicleRepository      $vehicleRepository,
        ExpenseRepository      $expenseRepository,
        GasStationRepository   $gasStationRepository
    )
    {
        $this->em = $entityManager;
        $this->vehicleRepository = $vehicleRepository;
        $this->expenseRepository = $expenseRepository;
        $this->gasStationRepository = $gasStationRepository;
    }

    /**
     * @throws InvalidArgument
     * @throws UnableToProcessCsv
     * @throws Exception
     */
    public function import(string $csvPath)
    {
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(";");

        $csvVehicle = [];
        $csvExpense = [];
        $csvGasStation = [];
        $cpt = 0;
        foreach ($csv as $row) {
            $csvVehicle = $this->getCurrentVehicle($row, $csvVehicle);
            $curentVehicle = $csvVehicle[$row["Immatriculation"]];

            $csvExpense = $this->getCurentExpense($row, $curentVehicle, $csvExpense);
            $curentExpense = $csvExpense[$row["Code dépense"]];

            $csvGasStation = $this->updateCsvGasStation($row, $curentExpense, $csvGasStation);

            if ($cpt % self::limiteFlush === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
        $this->em->clear();
    }

    private function getCurrentVehicle(array $row, array $csvVehicle): array
    {
        if (!in_array($row["Immatriculation"], $csvVehicle)) {
            $dbVehicle = $this->vehicleRepository->findOneBy(['plate_number' => $row["Immatriculation"]]);
            if ($dbVehicle) {
                $csvVehicle[$dbVehicle->getPlateNumber()] = $dbVehicle;
            }
            if (!$dbVehicle) {
                $curentVehicle = (new Vehicle())
                    ->setPlateNumber($row["Immatriculation"])
                    ->setBrand($row["Marque"])
                    ->setModel($row["Model"]);

                $csvVehicle[$curentVehicle->getPlateNumber()] = $curentVehicle;
                $this->em->persist($curentVehicle);
            }
        }

        return $csvVehicle;
    }

    /**
     * @throws \Exception
     */
    private function getCurentExpense(array $row, Vehicle $curentVehicle, array $csvExpense): array
    {
        if (!in_array($row["Code dépense"], $csvExpense)) {
            $dbExpense = $this->expenseRepository->findOneBy(['expense_number' => $row["Code dépense"]]);
            if ($dbExpense) {
                $csvExpense[$dbExpense->getExpenseNumber()] = $dbExpense;
            }

            if (!$dbExpense) {
                $issueOn = new DateTime($row["Date & heure"]);
                $curentExpense = (new Expense())
                    ->setCategory($row["Catégorie  de dépense"])
                    ->setValueTe($row["HT"])
                    ->setValueTi($row["TTC"])
                    ->setTaxRate($row["TVA"])
                    ->setIssuedOn($issueOn)
                    ->setInvoiceNumber($row["Numéro facture"])
                    ->setExpenseNumber($row["Code dépense"])
                    ->setVehicle($curentVehicle)
                    ->setDescription($row["Libellé"]);

                $csvExpense[$curentExpense->getExpenseNumber()] = $curentExpense;
                $this->em->persist($curentExpense);
            }
        }

        return $csvExpense;
    }

    private function updateCsvGasStation(array $row, Expense $curentExpense, array $csvGasStation): array
    {
        if (!in_array($curentExpense->getExpenseId(), $csvGasStation)) {
            $dbGasStation = $this->gasStationRepository->findOneBy(['expense_id' => $curentExpense->getExpenseId()]);
            if ($dbGasStation) {
                $csvGasStation[$curentExpense->getExpenseId()] = $dbGasStation;
            }

            if (!$dbGasStation) {
                $curentGasStation = (new GasStation())
                    ->setExpense($curentExpense)
                    ->setDescription($row["Station"])
                    ->setCoordinate(new Point($row["Position GPS (Latitude) "], $row["Position GPS (Longitude)"]));

                $csvGasStation[$curentExpense->getExpenseId()] = $curentGasStation;
                $this->em->persist($curentGasStation);
            }
        }

        return $csvGasStation;
    }
}