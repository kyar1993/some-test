<?php

namespace Classes;

use Exception;
use PDO;
use Throwable;

class Blacklists
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @var AdvertiserChecker
     */
    private $advertiserChecker;

    /**
     * @var string[]
     */
    private $prefixes = [
        'p' => 'publisher',
        's' => 'site',
        'publisher' => 'p',
        'site' => 's'
    ];

    /**
     * @var string патерн для проверки строк
     */
    private $pattern = "/^([s,p]{1}\d{1,})$/";

    public function __construct(
        PDO $db,
        AdvertiserChecker $advertiserChecker = null,
        PublishersChecker $publisherChecker = null,
        SitesChecker $sitesChecker = null
    )
    {
        $this->db = $db;
        $this->advertiserChecker = $advertiserChecker ?? new AdvertiserChecker($db);
    }

    /**
     * Блэклисты рекламодателя из БД
     *
     * @param int $advertiserId
     * @return string
     * @throws Throwable
     */
    public function get(int $advertiserId): string
    {
        try {
            $res = $this->db->query("SELECT advertiser, entity, type
FROM blacklists bl WHERE bl.advertiser = {$advertiserId}", PDO::FETCH_ASSOC);

            if ($res->rowCount() === 0) {
                return '';
            }

            $list = [];

            $res = $res->fetchAll();

            foreach ($res as $row) {
                $list[] = $this->prefixes[$row['type']] . $row['entity'];
            }

            return implode(', ', $list);
        } catch (Throwable $t) {
            throw $t;
        }
    }

    /**
     * Добавление в блеклист рекламодателя в БД
     *
     * @param string $blackLists строка с элементами
     * @param int $advertiserId id рекламодателя
     * @throws Throwable
     */
    public function save(string $blackLists, int $advertiserId): void
    {
        try {
            // 2. проверяем существование рекламодателя
            $this->advertiserChecker->handler($advertiserId);

            $list = explode(',', str_replace(" ", '', $blackLists));

            // убираем дубли из запроса
            $list = array_unique($list);

            $dataForWriting = [];
            $entitiesExists = [
                'publisher' => [],
                'site' => []
            ];

            foreach ($list as $item) {
                // 1. проверяем соответствие формату;
                if (preg_match($this->pattern, $item) !== 1) {
                    throw new Exception("Значение '{$item}' не соответствует формату!!! (пример: 's123')");
                }

                // получаем префикс записи
                $prefix = mb_substr($item, 0, 1);

                // получаем тип записи
                $type = $this->prefixes[$prefix];

                // получаем id записи
                $id = intval(mb_substr($item, 1));

                // добавляем в список ids для проверки существования записей в бд
                $entitiesExists[$type][] = $id;

                // добавляем в список данных на запись
                $dataForWriting[] = [
                    'advertiser' => $advertiserId,
                    'id' => $id,
                    'type' => $type
                ];
            }

            if (count($entitiesExists['publisher'])) {
                // 3. проверяем существование id сайтов (sites) и паблишеров (publishers)
                (new PublishersChecker(
                    $this->db
                ))->handler(
                    $entitiesExists['publisher']
                );
            }

            if (count($entitiesExists['site'])) {
                (new SitesChecker(
                    $this->db
                ))->handler(
                    $entitiesExists['site']
                );
            }

            // пишем данные в БД (blacklists)
            (new WritingIntoDb(
                $this->db
            ))->handler($dataForWriting);
        } catch (Throwable $t) {
            throw $t;
        }
    }
}
