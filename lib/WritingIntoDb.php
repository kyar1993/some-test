<?php

namespace Classes;

use PDO;

/**
 * Добавляем записи в БД
 *
 * Class WritingIntoDb
 * @package Classes
 *
 * @author Yaroshevich Konstantin <yaroshevich@citylink.pro>
 */
class WritingIntoDb
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
     * @param array $entities
     */
    public function handler(array $entities)
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO blacklists (advertiser, entity, type)
VALUES (:advertiser, :entity, :type)");

        foreach ($entities as $entity) {
            $stmt->bindValue(':advertiser', $entity['advertiser'], PDO::PARAM_INT);
            $stmt->bindValue(':entity', $entity['id'], PDO::PARAM_INT);
            $stmt->bindValue(':type', $entity['type'], PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
