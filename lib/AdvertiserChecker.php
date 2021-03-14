<?php

namespace Classes;

use Exception;
use PDO;

/**
 * Проверка существования рекламодателя
 *
 * Class AdvertiserChecker
 * @package Classes
 *
 * @author Yaroshevich Konstantin <yaroshevich@citylink.pro>
 */
class AdvertiserChecker
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
     * @param int $id
     * @throws Exception
     */
    public function handler(int $id)
    {
        $query = $this->db->query(
            "SELECT 1 FROM advertisers a WHERE a.id = {$id} LIMIT 1",
            PDO::FETCH_ASSOC
        );

        // нет - икслючение
        if (!$query->fetch()) {
            throw new Exception("Рекламодатель с id = {$id}, не существует!!!");
        }
    }
}