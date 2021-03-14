<?php

namespace Classes;

use Exception;
use PDO;

/**
 * Class PublishersChecker
 * @package Classes
 *
 * @author Yaroshevich Konstantin <yaroshevich@citylink.pro>
 */
class PublishersChecker
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
     * @throws Exception
     */
    public function handler(array $identificators)
    {
        $q = 'SELECT group_concat(id separator ",") as ids 
FROM publishers p WHERE p.id IN (' . implode(',', $identificators) . ')';

        $query = $this->db->query(
            $q,
            PDO::FETCH_ASSOC
        );

        $res = $query->fetch();

        if (!isset($res['ids']) || !$res['ids']) {
            throw new Exception("Не удалось найти публикаторов!!!");
        }

        // проверяем наличие элементов в publishers
        (new DiffChecker($this->db))->handler(
            $identificators,
            $res['ids'],
            'publisher'
        );
    }
}
