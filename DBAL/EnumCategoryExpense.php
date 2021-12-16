<?php

use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumCategoryExpense extends \Doctrine\DBAL\Types\Type
{
    const ENUM_EXPENSE = 'enumcategoryexpense';
    const CAT_GASOLINE = 'gasoline';
    const CAT_DIESEL = 'diesel';
    const CAT_ELECTRIK = 'electricity_charge';
    const CAT_GPL = 'gpl';
    const CAT_HYDROGEN = 'hydrogen';

    const values = [
        self::CAT_GASOLINE,
        self::CAT_DIESEL,
        self::CAT_ELECTRIK,
        self::CAT_GPL,
        self::CAT_HYDROGEN,
    ];

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return "ENUM('gasoline','diesel','electricity_charge','gpl','hydrogen')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, self::values)){
            throw new \http\Exception\InvalidArgumentException("Invalid");
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::ENUM_EXPENSE;
    }
}