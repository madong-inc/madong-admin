<?php

namespace core\utils;

use RuntimeException;
use InvalidArgumentException;

class Snowflake
{
    const EPOCH = 1704038400000; // 2024-01-01 00:00:00 的 Unix 时间戳（毫秒）
    const WORKER_ID_BITS = 5;
    const DATA_CENTER_ID_BITS = 5;
    const SEQUENCE_BITS = 13;

    const MAX_WORKER_ID = (1 << self::WORKER_ID_BITS) - 1;
    const MAX_DATA_CENTER_ID = (1 << self::DATA_CENTER_ID_BITS) - 1;
    const SEQUENCE_MASK = (1 << self::SEQUENCE_BITS) - 1;

    private int $workerId;
    private int $dataCenterId;
    private int $sequence = 0;
    private int $lastTimestamp = -1;
    private array $idBuffer = [];
    private int $bufferSize = 10;

    // 时钟回拨容忍时间（毫秒）
    private int $timeBackwardsTolerance = 50;
    // 是否启用休眠等待
    private bool $enableSleepWait;
    // 是否使用高性能时钟
    private bool $useHighPerfClock;

    /**
     * @param int $workerId 0-31
     * @param int $dataCenterId 0-31
     * @param bool $enableSleepWait 是否启用休眠等待（默认true）
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $workerId,
        int $dataCenterId,
        bool $enableSleepWait = true,
        bool $useHighPerfClock = false,
        int $bufferSize = 10
    ) {
        if ($workerId > self::MAX_WORKER_ID || $workerId < 0) {
            throw new InvalidArgumentException("Worker ID can't be greater than " . self::MAX_WORKER_ID . " or less than 0");
        }
        if ($dataCenterId > self::MAX_DATA_CENTER_ID || $dataCenterId < 0) {
            throw new InvalidArgumentException("Data Center ID can't be greater than " . self::MAX_DATA_CENTER_ID . " or less than 0");
        }

        $this->workerId = $workerId;
        $this->dataCenterId = $dataCenterId;
        $this->enableSleepWait = $enableSleepWait;
        $this->useHighPerfClock = $useHighPerfClock;
        $this->bufferSize = max(1, min(100, $bufferSize));
    }

    /**
     * 获取下一个唯一 ID
     *
     * @return string
     * @throws RuntimeException
     */
    /**
     * 批量生成ID (减少锁竞争)
     */
    public function nextIds(int $count = 1): array
    {
        $count = max(1, min(100, $count));
        $ids = [];
        while ($count-- > 0) {
            $ids[] = $this->generateId();
        }
        return $ids;
    }

    public function nextId(): string
    {
        if (!empty($this->idBuffer)) {
            return array_shift($this->idBuffer);
        }
        $this->idBuffer = $this->nextIds($this->bufferSize);
        return $this->nextId();
    }

    private function generateId(): string
    {
        $timestamp = $this->timeGen();

        if ($timestamp < $this->lastTimestamp - $this->timeBackwardsTolerance) {
            throw new RuntimeException("时钟回拨超过容忍范围: " . ($this->lastTimestamp - $timestamp) . "ms");
        }

        if ($timestamp == $this->lastTimestamp) {
            $this->sequence = ($this->sequence + 1) & self::SEQUENCE_MASK;
            if ($this->sequence === 0) {
                // 等待下一毫秒
                $timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        $timestampDelta = $timestamp - self::EPOCH;
        $workerIdShift = self::SEQUENCE_BITS;
        $dataCenterIdShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampShift = self::DATA_CENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        $id = ($timestampDelta << $timestampShift)
            | ($this->dataCenterId << $dataCenterIdShift)
            | ($this->workerId << $workerIdShift)
            | $this->sequence;

        return (string)$id;
    }

    /**
     * 解析Snowflake ID
     */
    public static function parseId(string $id): array
    {
        $id = (int)$id;
        return [
            'timestamp' => ($id >> 22) + self::EPOCH,
            'dataCenterId' => ($id >> 17) & 0x1F,
            'workerId' => ($id >> 12) & 0x1F,
            'sequence' => $id & 0xFFF,
            'datetime' => date('Y-m-d H:i:s', (($id >> 22) + self::EPOCH) / 1000)
        ];
    }

    /**
     * 返回固定长度的 ID（左补 0）
     *
     * @param int $length
     * @return string
     */
    public function nextFixedLengthId(int $length): string
    {
        return str_pad($this->nextId(), $length, '0', STR_PAD_LEFT);
    }

    /**
     * 获取当前时间戳（毫秒）
     *
     * @return int
     */
    private function timeGen(): int
    {
        if ($this->useHighPerfClock && function_exists('hrtime')) {
            return (int)(hrtime(true) / 1e6); // 纳秒转毫秒
        }
        return (int)(microtime(true) * 1000);
    }

    /**
     * 等待下一毫秒
     *
     * @param int $lastTimestamp
     * @return int
     */
    private function tilNextMillis(int $lastTimestamp): int
    {
        $timestamp = $this->timeGen();
        if ($this->enableSleepWait) {
            $sleepUs = ($lastTimestamp - $timestamp + 1) * 1000;
            usleep(max(100, $sleepUs)); // 最小休眠100us
            return $this->timeGen();
        }

        // 兼容模式：忙等待
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }
        return $timestamp;
    }
}
