<?php

namespace Oxygen\Data\Tests\Behaviour;

use DateTime;
use Oxygen\Data\Behaviour\Timestamps;
use PHPUnit\Framework\TestCase;

class TimestampsTest extends TestCase {

    private function entity(): object {
        return new class {
            use Timestamps;
        };
    }

    public function testPrePersistSetsBothTimestamps(): void {
        $e = $this->entity();
        $before = new DateTime();

        $e->prePersist();

        $this->assertGreaterThanOrEqual($before->getTimestamp(), $e->getCreatedAt()->getTimestamp());
        $this->assertEquals($e->getCreatedAt(), $e->getUpdatedAt());
    }

    public function testPreUpdateOnlyTouchesUpdatedAt(): void {
        $e = $this->entity();
        $e->setCreatedAt(new DateTime('2020-01-01'));
        $e->setUpdatedAt(new DateTime('2020-01-01'));

        $e->preUpdate();

        $this->assertEquals(new DateTime('2020-01-01'), $e->getCreatedAt());
        $this->assertGreaterThan(new DateTime('2020-01-02'), $e->getUpdatedAt());
    }

    public function testPreserveUpdatedAtSurvivesPersistAndUpdate(): void {
        $e = $this->entity();
        $e->setUpdatedAt(new DateTime('2020-01-01'));
        $e->preserveUpdatedAt();

        $e->prePersist();
        $this->assertEquals(new DateTime('2020-01-01'), $e->getUpdatedAt());

        $e->preUpdate();
        $this->assertEquals(new DateTime('2020-01-01'), $e->getUpdatedAt());
        $this->assertGreaterThan(new DateTime('2020-01-02'), $e->getCreatedAt());
    }
}
