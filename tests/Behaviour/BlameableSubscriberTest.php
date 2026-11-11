<?php

namespace Oxygen\Data\Tests\Behaviour;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Oxygen\Data\Behaviour\Blameable;
use Oxygen\Data\Behaviour\BlameableSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Guards the ORM 3 event-args API (PrePersistEventArgs / PreUpdateEventArgs, getObject()).
 */
class BlameableSubscriberTest extends TestCase {

    private Authenticatable $user;
    private BlameableSubscriber $subscriber;

    protected function setUp(): void {
        $this->user = $this->createMock(Authenticatable::class);
        $guard = $this->createMock(Guard::class);
        $guard->method('user')->willReturn($this->user);
        $auth = $this->createMock(AuthManager::class);
        $auth->method('guard')->willReturn($guard);
        $this->subscriber = new BlameableSubscriber($auth);
    }

    private function blameable(): Blameable {
        return new class implements Blameable {
            public ?Authenticatable $createdBy = null;
            public ?Authenticatable $updatedBy = null;
            public function getCreatedBy(): ?Authenticatable { return $this->createdBy; }
            public function getUpdatedBy(): ?Authenticatable { return $this->updatedBy; }
            public function setCreatedBy(Authenticatable $createdBy) { $this->createdBy = $createdBy; }
            public function setUpdatedBy(Authenticatable $updatedBy) { $this->updatedBy = $updatedBy; }
        };
    }

    public function testSubscribesToPersistAndUpdate(): void {
        $this->assertEqualsCanonicalizing(['prePersist', 'preUpdate'], $this->subscriber->getSubscribedEvents());
    }

    public function testPrePersistSetsCreatedByAndUpdatedBy(): void {
        $entity = $this->blameable();

        $this->subscriber->prePersist(new PrePersistEventArgs($entity, $this->createMock(EntityManagerInterface::class)));

        $this->assertSame($this->user, $entity->createdBy);
        $this->assertSame($this->user, $entity->updatedBy);
    }

    public function testPreUpdateOnlySetsUpdatedBy(): void {
        $entity = $this->blameable();
        $changeSet = [];

        $this->subscriber->preUpdate(new PreUpdateEventArgs($entity, $this->createMock(EntityManagerInterface::class), $changeSet));

        $this->assertNull($entity->createdBy);
        $this->assertSame($this->user, $entity->updatedBy);
    }

    public function testIgnoresEntitiesThatAreNotBlameable(): void {
        $entity = new \stdClass();

        $this->subscriber->prePersist(new PrePersistEventArgs($entity, $this->createMock(EntityManagerInterface::class)));

        $this->assertEquals(new \stdClass(), $entity);
    }
}
