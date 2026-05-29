<?php

use App\Models\RouteModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class RouteScheduleConflictTest extends CIUnitTestCase
{
    public function testBusyWindowKeepsDriverUnavailableUntilThirtyMinutesAfterArrival(): void
    {
        $departure = strtotime('2026-05-25 09:00:00');
        $arrival = $departure + 60 * 60;
        [$start, $end] = $this->routeBusyWindow(
            date('Y-m-d H:i:s', $departure),
            date('Y-m-d H:i:s', $arrival)
        );

        $this->assertSame($departure, $start);
        $this->assertSame($arrival + 30 * 60, $end);
    }

    public function testBusyWindowUsesLatestDeliveryWhenItIsAfterRouteArrival(): void
    {
        $departure = strtotime('2026-05-25 09:00:00');
        $arrival = $departure + 60 * 60;
        $latestDelivery = $arrival + 20 * 60;
        [, $end] = $this->routeBusyWindow(
            date('Y-m-d H:i:s', $departure),
            date('Y-m-d H:i:s', $arrival),
            [date('Y-m-d H:i:s', $latestDelivery)]
        );

        $this->assertSame($latestDelivery + 30 * 60, $end);
    }

    public function testDepartureAtReturnBufferBoundaryDoesNotOverlap(): void
    {
        $departure = strtotime('2026-05-25 09:00:00');
        $arrival = $departure + 60 * 60;
        $firstAllowedDeparture = $arrival + 30 * 60;

        [, $existingEnd] = $this->routeBusyWindow(
            date('Y-m-d H:i:s', $departure),
            date('Y-m-d H:i:s', $arrival)
        );
        [$newStartBeforeBuffer] = $this->routeBusyWindow(
            date('Y-m-d H:i:s', $firstAllowedDeparture - 60),
            date('Y-m-d H:i:s', $firstAllowedDeparture + 30 * 60)
        );
        [$newStartAtBuffer] = $this->routeBusyWindow(
            date('Y-m-d H:i:s', $firstAllowedDeparture),
            date('Y-m-d H:i:s', $firstAllowedDeparture + 30 * 60)
        );

        $this->assertLessThan($existingEnd, $newStartBeforeBuffer);
        $this->assertSame($existingEnd, $newStartAtBuffer);
    }

    private function routeBusyWindow(string $departureDate, ?string $estimatedArrival = null, array $deliveryTimes = []): array
    {
        $method = new ReflectionMethod(RouteModel::class, 'routeBusyWindow');
        $method->setAccessible(true);

        return $method->invoke(new RouteModel(), $departureDate, $estimatedArrival, $deliveryTimes);
    }
}
