<?php

declare(strict_types=1);

namespace Siler\Test\Unit;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Siler\Monolog as Log;
use function Siler\Functional\always;

class MonologTest extends TestCase
{
    public function testStream()
    {
        $handler = Log\stream('php://output');
        $this->assertInstanceOf(StreamHandler::class, $handler);
    }

    public function testLog()
    {
        $handler = new TestHandler();

        Log\handler($handler);
        Log\log(Logger::WARNING, 'test');

        list($record) = $handler->getRecords();

        $this->assertSame(Log\MONOLOG_DEFAULT_CHANNEL, $record['channel']);
    }

    public function testSugar()
    {
        $handler = new TestHandler();
        Log\handler($handler, 'test');

        $levels = array_values(Logger::getLevels());

        foreach ($levels as $level) {
            $levelName = strtolower(Logger::getLevelName($level));
            call_user_func("Siler\\Monolog\\$levelName", $levelName, ['context' => $levelName], 'test');
        }

        $records = $handler->getRecords();

        foreach ($levels as $i => $level) {
            $levelName = Logger::getLevelName($level);
            $record = $records[$i];

            $this->assertSame(strtolower($levelName), $record['message']);
            $this->assertSame($level, $record['level']);
            $this->assertSame($levelName, $record['level_name']);
            $this->assertArrayHasKey('context', $record['context']);
            $this->assertSame(strtolower($levelName), $record['context']['context']);
            $this->assertSame('test', $record['channel']);
        }
    }

    public function testLogIf()
    {
        $handler = new TestHandler();
        $channel = 'log_if';

        Log\handler($handler, $channel);
        Log\debug('foo', [], $channel);
        Log\debug_if(always(false));
        Log\debug('bar', [], $channel);

        $records = $handler->getRecords();

        $this->assertCount(1, $records);
        $this->assertSame('foo', $records[0]['message']);
    }
}
