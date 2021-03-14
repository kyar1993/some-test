<?php

namespace Classes;

use Exception;
use PDO;

/**
 * Получение расхождения массивов
 *
 * Class DiffChecker
 * @package Classes
 *
 * @author Yaroshevich Konstantin <yaroshevich@citylink.pro>
 */
class DiffChecker
{
    /**
     * @var PDO
     */
    private $db;

    public function __construct(
        PDO $db
    )
    {
        $this->db = $db;
    }

    /**
     * @param array $identificators
     * @param string $ids
     * @param string $type
     * @throws Exception
     */
    public function handler(array $identificators, string $ids, string $type)
    {
        $diff = array_diff(
            $identificators,
            explode(',', $ids)
        );

        // есть различия в массивах - каких-то id нет в бд
        foreach ($diff as $item) {
            throw new Exception("Элемент '{$type}', c id = '{$item}' не найден в БД!!!");

        }
    }
}
