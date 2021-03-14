<?php

use Classes\AdvertiserChecker;
use Classes\Blacklists;
use Classes\PublishersChecker;
use Classes\SitesChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlacklistsTest extends TestCase
{
    /**
     * @var int
     */
    private $advertiserId;

    /**
     * @var AdvertiserChecker|MockObject
     */
    private $advertiserChecker;

    /**
     * @var PublishersChecker|MockObject
     */
    private $publisherChecker;

    /**
     * @var SitesChecker|MockObject
     */
    private $sitesChecker;

    /**
     * @var PDO|MockObject
     */
    private $db;

    protected function setUp(): void
    {
        $this->advertiserId = 1;
        $this->advertiserChecker = $this->createMock(AdvertiserChecker::class);
        $this->publisherChecker = $this->createMock(PublishersChecker::class);
        $this->sitesChecker = $this->createMock(SitesChecker::class);

        $this->db = $this->createMock(PDO::class);
    }

    protected function tearDown(): void
    {
        $this->advertiserId = NULL;
        $this->advertiserChecker = NULL;
        $this->db = NULL;
    }

    /**
     * Пустой ответ при получении списка
     *
     * @throws Throwable
     */
    public function testEmptyGetStub()
    {
        $state = $this->createMock(PDOStatement::class);
        $state->method('rowCount')
            ->willReturn(0);

        // Настроить заглушку.
        $this->db->method('query')
            ->willReturn($state);

        $bl = new Blacklists($this->db);

        $this->assertSame('', $bl->get($this->advertiserId));
    }

    /**
     * Корректное получение списка
     *
     * @throws Throwable
     */
    public function testCorrectGet()
    {
        $state = $this->createMock(PDOStatement::class);

        // Настроить заглушку.
        $this->db->method('query')
            ->willReturn($state);

        $state->method('rowCount')
            ->willReturn(3);

        $state->method('fetchAll')
            ->willReturn([
                0 => [
                    "advertiser" => "1",
                    "entity" => "1",
                    "type" => "publisher"
                ],
                1 => [
                    "advertiser" => "1",
                    "entity" => "2",
                    "type" => "publisher"
                ],
                2 => [
                    "advertiser" => "1",
                    "entity" => "111",
                    "type" => "site"
                ]
            ]);

        $bl = new Blacklists($this->db);

        $this->assertSame('p1, p2, s111', $bl->get($this->advertiserId));
    }

    /**
     * Проверка существования рекламодателя
     *
     * @throws Throwable
     */
    public function testAdvertiserCheckException()
    {
        try {
            $exception = new Exception("Рекламодатель с id = {$this->advertiserId}, не существует!!!");

            $this->advertiserChecker->method('handler')
                ->willThrowException($exception);

            $bl = new Blacklists(
                $this->db,
                $this->advertiserChecker
            );

            $state = $this->createMock(PDOStatement::class);

            $this->db->method('query')
                ->willReturn($state);

            $state->method('fetch')
                ->willReturn(false);

            $bl->save("s111", $this->advertiserId);
        } catch (Throwable $t) {
            $this->assertSame("Рекламодатель с id = 1, не существует!!!", $t->getMessage());
        }
    }

    /**
     * Тест проверки некорректного значения
     */
    public function testValueException()
    {
        try {
            (new Blacklists(
                $this->db,
                $this->advertiserChecker
            ))->save("ss111", $this->advertiserId);
        } catch (Throwable $t) {
            $this->assertSame(
                "Значение 'ss111' не соответствует формату!!! (пример: 's123')",
                $t->getMessage()
            );
        }
    }

    /**
     * Исключение проверки публикатора
     */
    public function testPublishersCheckerException()
    {
        try {
            $state = $this->createMock(PDOStatement::class);

            $this->db->method('query')
                ->willReturn($state);

            $state->method('fetch')
                ->willReturn(false);

            (new Blacklists(
                $this->db,
                $this->advertiserChecker,
                $this->publisherChecker,
                $this->sitesChecker
            ))->save("p1", $this->advertiserId);
        } catch (Throwable $t) {
            $this->assertSame(
                "Не удалось найти публикаторов!!!",
                $t->getMessage()
            );
        }
    }

    /**
     * Исключение проверки сайтов
     */
    public function testSitesCheckerException()
    {
        try {
            $state = $this->createMock(PDOStatement::class);

            $this->db->method('query')
                ->willReturn($state);

            $state->method('fetch')
                ->willReturn(false);

            (new Blacklists(
                $this->db,
                $this->advertiserChecker,
                $this->publisherChecker,
                $this->sitesChecker
            ))->save("s333", $this->advertiserId);
        } catch (Throwable $t) {
            $this->assertSame(
                "Не удалось найти сайты!!!",
                $t->getMessage()
            );
        }
    }
}
