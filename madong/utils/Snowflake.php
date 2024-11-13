<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.tech
 */

namespace madong\utils;

use madong\exception\ApiException;

/**
 *
 * 雪花ID生成
 * @author Mr.April
 * @since  1.0
 */
class Snowflake
{
    const EPOCH = 1704038400000; // 2024-01-01 00:00:00 的 Unix 时间戳（毫秒）
    const WORKER_ID_BITS = 5;
    const DATA_CENTER_ID_BITS = 5;
    const SEQUENCE_BITS = 13;

    const MAX_WORKER_ID = (1 << self::WORKER_ID_BITS) - 1;
    const MAX_DATA_CENTER_ID = (1 << self::DATA_CENTER_ID_BITS) - 1;

    private int $workerId;
    private int $dataCenterId;
    private int $sequence = 0;
    private int $lastTimestamp = -1;

    /**
     * @throws \madong\exception\ApiException
     */
    public function __construct(int $workerId, int $dataCenterId)
    {
        if ($workerId > self::MAX_WORKER_ID || $workerId < 0) {
            throw new ApiException("Worker ID can't be greater than " . self::MAX_WORKER_ID . " or less than 0");
        }
        if ($dataCenterId > self::MAX_DATA_CENTER_ID || $dataCenterId < 0) {
            throw new ApiException("Data Center ID can't be greater than " . self::MAX_DATA_CENTER_ID . " or less than 0");
        }
        $this->workerId     = $workerId;
        $this->dataCenterId = $dataCenterId;
    }

    /**
     * @throws \madong\exception\ApiException
     */
    public function nextId(): int
    {
        $timestamp = $this->timeGen();

        if ($timestamp < $this->lastTimestamp) {
            throw new ApiException("Clock moved backwards. Refusing to generate id for " . ($this->lastTimestamp - $timestamp) . " milliseconds");
        }

        if ($this->lastTimestamp == $timestamp) {
            $this->sequence = ($this->sequence + 1) & ((1 << self::SEQUENCE_BITS) - 1);
            if ($this->sequence == 0) {
                $timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        $timestampDelta    = $timestamp - self::EPOCH;
        $workerIdShift     = self::SEQUENCE_BITS;
        $dataCenterIdShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampShift    = self::DATA_CENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        return ($timestampDelta << $timestampShift)
            | ($this->dataCenterId << $dataCenterIdShift)
            | ($this->workerId << $workerIdShift)
            | $this->sequence;
    }

    private function timeGen(): int
    {
        return floor(microtime(true) * 1000);
    }

    private function tilNextMillis($lastTimestamp): int
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }
        return $timestamp;
    }

    public function nextFixedLengthId(int $length): string
    {
        $id = $this->nextId();
        return str_pad($id, $length, '0', STR_PAD_LEFT);
    }
}
