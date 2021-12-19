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
    const limiteFlush = 200;

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
            $cpt++;

            $csvVehicle = $this->getCurrentVehicle($row, $csvVehicle);
            $curentVehicle = $csvVehicle[$row["Immatriculation"]];

            try{
                $csvExpense = $this->getCurentExpense($row, $curentVehicle, $csvExpense);
            } catch (\Exception $e) {
                //log error
                $this->save($cpt);
                continue;
            }
            $curentExpense = $csvExpense[$row["Code dépense"]];

            try {
                $csvGasStation = $this->updateCsvGasStation($row, $curentExpense, $csvGasStation);
            } catch (\Exception $e) {
                //log error
                $this->save($cpt);
                continue;
            }

            $this->save($cpt);
        }
        $this->em->flush();
        $this->em->clear();
    }

    private function getCurrentVehicle(array $row, array $csvVehicle): array
    {
        if (!in_array($row["Immatriculation"], $csvVehicle)) {
            $dbVehicle = $this->vehicleRepository->findOneBy(['plateNumber' => $row["Immatriculation"]]);
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
            $dbExpense = $this->expenseRepository->findOneBy(['expenseNumber' => $row["Code dépense"]]);
            if ($dbExpense) {
                $csvExpense[$dbExpense->getExpenseNumber()] = $dbExpense;
            }

            if (!$dbExpense) {
                if(preg_match('$^2([0-9]{3})-([0-1][0-9])-([0-3][0-9])\s([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$',$row["Date & heure"])){
                    $issueOn = new DateTime($row["Date & heure"]);
                } else {
                    throw new \InvalidArgumentException("Format date incorrect");
                }
                $curentExpense = (new Expense())
                    ->setCategory($row["Catégorie  de dépense"])
                    ->setValueTe(floatval(str_replace(',', '.',$row["HT"])))
                    ->setValueTi(floatval(str_replace(',', '.',$row["TTC"])))
                    ->setTaxRate(floatval(str_replace(',', '.',$row["TVA"])))
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
        if (!in_array($curentExpense->getExpenseNumber(), $csvGasStation)) {
            $dbGasStation = $this->gasStationRepository->findOneBy(['expense' => $curentExpense]);
            if ($dbGasStation) {
                $csvGasStation[$curentExpense->getExpenseNumber()] = $dbGasStation;
            }

            if (!$dbGasStation) {
                $curentGasStation = (new GasStation())
                    ->setExpense($curentExpense)
                    ->setDescription($row["Station"])
                    ->setCoordinate(new Point($row["Position GPS (Longitude)"], $row["Position GPS (Latitude) "]));

                $csvGasStation[$curentExpense->getExpenseNumber()] = $curentGasStation;
                $this->em->persist($curentGasStation);
            }
        }

        return $csvGasStation;
    }

    /**
     * @param int $cpt
     * @return void
     */
    public function save(int $cpt): void
    {
        if ($cpt % self::limiteFlush === 0) {
            $this->em->flush();
            $this->em->clear();
        }
    }
}