<?php

namespace Oxygen\Data\Tests\Repository;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Oxygen\Data\Behaviour\Versionable;
use Oxygen\Data\Repository\Doctrine\Versions;
use PHPUnit\Framework\TestCase;

/**
 * `needsNewVersion()` compares Carbon::diffInHours() against 24. Carbon 3 returns signed floats,
 * so the direction of the comparison and the boundary matter.
 */
class VersionsTest extends TestCase {

    protected function tearDown(): void {
        Carbon::setTestNow();
    }

    private function needsNewVersion(Versionable $entity): bool {
        $host = new class {
            use Versions;
            public function check(Versionable $e): bool { return $this->needsNewVersion($e); }
        };
        return $host->check($entity);
    }

    private function entity(bool $head, ?Carbon $latestVersionUpdatedAt): Versionable {
        $entity = $this->createMock(Versionable::class);
        $entity->method('isHead')->willReturn($head);
        $entity->method('hasVersions')->willReturn($latestVersionUpdatedAt !== null);
        $version = new class($latestVersionUpdatedAt) {
            public function __construct(private ?Carbon $at) {}
            public function getUpdatedAt(): ?Carbon { return $this->at; }
        };
        $entity->method('getVersions')->willReturn(new ArrayCollection($latestVersionUpdatedAt ? [$version] : []));
        return $entity;
    }

    public function testNonHeadEntitiesNeverGetANewVersion(): void {
        $this->assertFalse($this->needsNewVersion($this->entity(false, Carbon::now()->subDays(10))));
    }

    public function testFirstVersionIsAlwaysCreated(): void {
        $this->assertTrue($this->needsNewVersion($this->entity(true, null)));
    }

    public function testRecentVersionIsNotDuplicated(): void {
        Carbon::setTestNow('2026-01-10 12:00:00');
        $this->assertFalse($this->needsNewVersion($this->entity(true, Carbon::now()->subHours(23)->subMinutes(59))));
    }

    public function testVersionOlderThan24HoursTriggersANewOne(): void {
        Carbon::setTestNow('2026-01-10 12:00:00');
        $this->assertTrue($this->needsNewVersion($this->entity(true, Carbon::now()->subHours(24))));
        $this->assertTrue($this->needsNewVersion($this->entity(true, Carbon::now()->subDays(30))));
    }
}
