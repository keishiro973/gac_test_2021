<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\GasStation;
use App\Entity\Vehicle;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use League\Csv\Reader;

class ImportCsv
{
    const limiteFlush = 20;

    public function import(string $csvPath)
    {
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(";");

        $csvRows = $csv->getRecords();
        foreach ($csvRows as $offset => $row) {
            //vehicle
            $vehicle = new Vehicle();
            $vehicle
                ->setPlateNumber($row["Immatriculation"])
                ->setBrand($row["Marque"])
                ->setModel($row["Model"]);
dump($vehicle);
            //Expense
            $expense = new Expense();
            $issueOn = new \DateTime($row["Date & heure"]);
            $expense
                ->setCategory($row["Catégorie  de dépense"])
                ->setValueTe($row["HT"])
                ->setValueTi($row["TTC"])
                ->setTaxRate($row["TVA"])
                ->setIssuedOn($issueOn)
                ->setInvoiceNumber($row["Numéro facture"])
                ->setExpenseNumber($row["Code dépense"])
                ->setVehicle($vehicle)
                ->setDescription($row["Libellé"]);
dump($expense);
            //Gas Station
            $gasStation = new GasStation();
            $gasStation
                ->setExpense($expense)
                ->setDescription($row["Station"]);
//            $point = new Point();
//            $point->setX($row["Position GPS (Latitude) "])->setY($row["Position GPS (Longitude)"]);
//            $gasStation->setCoordinate($point);
dump($gasStation);

            die;
        }
    }
}