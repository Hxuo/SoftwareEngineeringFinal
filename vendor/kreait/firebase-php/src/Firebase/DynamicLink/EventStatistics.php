<?php

declare(strict_types=1);

namespace Kreait\Firebase\DynamicLink;

use Countable;
use IteratorAggregate;
use Traversable;

use function array_filter;

/**
 * @see https://firebase.google.com/docs/reference/dynamic-links/analytics#response_body
 * @see https://github.com/googleapis/google-api-nodejs-client/blob/main/src/apis/firebasedynamiclinks/v1.ts
 *
 * @phpstan-type EventStatisticsShape array{
 *     linkEventStats: array{
 *         count?: non-empty-string,
 *         event?: non-empty-string,
 *         platform?: non-empty-string
 *     }
 * }
 *
 * @implements IteratorAggregate<array>
 */
final class EventStatistics implements Countable, IteratorAggregate
{
    public const PLATFORM_ANDROID = 'ANDROID';

    public const PLATFORM_DESKTOP = 'DESKTOP';

    public const PLATFORM_IOS = 'IOS';

    // Any click on a Dynamic Link, irrespective to how it is handled and its destinations
    public const TYPE_CLICK = 'CLICK';

    // Attempts to redirect users, either to the App Store or Play Store to install or update the app,
    // or to some other destination
    public const TYPE_REDIRECT = 'REDIRECT';

    // Actual installs (only supported by the Play Store)
    public const TYPE_APP_INSTALL = 'APP_INSTALL';

    // First-opens after an install
    public const TYPE_APP_FIRST_OPEN = 'APP_FIRST_OPEN';

    // Re-opens of an app
    public const TYPE_APP_RE_OPEN = 'APP_RE_OPEN';

    /**
     * @param list<EventStatisticsShape> $events
     */
    private function __construct(private readonly array $events)
    {
    }

    /**
     * @param list<EventStatisticsShape> $events
     */
    public static function fromArray(array $events): self
    {
        return new self($events);
    }

    public function onAndroid(): self
    {
        return $this->filterByPlatform(self::PLATFORM_ANDROID);
    }

    public function onDesktop(): self
    {
        return $this->filterByPlatform(self::PLATFORM_DESKTOP);
    }

    public function onIOS(): self
    {
        return $this->filterByPlatform(self::PLATFORM_IOS);
    }

    public function clicks(): self
    {
        return $this->filterByType(self::TYPE_CLICK);
    }

    public function redirects(): self
    {
        return $this->filterByType(self::TYPE_REDIRECT);
    }

    public function appInstalls(): self
    {
        return $this->filterByType(self::TYPE_APP_INSTALL);
    }

    public function appFirstOpens(): self
    {
        return $this->filterByType(self::TYPE_APP_FIRST_OPEN);
    }

    public function appReOpens(): self
    {
        return $this->filterByType(self::TYPE_APP_RE_OPEN);
    }

    public function filterByType(string $type): self
    {
        return $this->filter(static fn(array $event): bool => ($event['event'] ?? null) === $type);
    }

    public function filterByPlatform(string $platform): self
    {
        return $this->filter(static fn(array $event): bool => ($event['platform'] ?? null) === $platform);
    }

    public function filter(callable $filter): self
    {
        return new self(array_values(array_filter($this->events, $filter)));
    }

    /**
     * @return Traversable<EventStatisticsShape>
     */
    public function getIterator(): Traversable
    {
        yield from $this->events;
    }

    public function count(): int
    {
        return array_sum(array_column($this->events, 'count'));
    }
}
