<?php
/*
 * This file is part of the prooph/php-ddd-cargo-sample.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 01.03.14 - 22:35
 */
declare(strict_types = 1);

namespace Codeliner\GraphTraversalBackend;

use Codeliner\GraphTraversalBackend\Dto\EdgeDto;
use Codeliner\GraphTraversalBackend\Dto\TransitPathDto;

/**
 * Class GraphTraversalService
 *
 * The GraphTraversalService takes a RouteSpecification, that describes the start location and the end location of a cargo
 * and fetches compatible routes that are described by Itineraries.
 *
 * @package Application\Service
 * @author Alexander Miertsch <contact@prooph.de>
 */
class GraphTraversalService implements GraphTraversalServiceInterface
{
    /**
     * @var array
     */
    private $routes = array();

    public function __construct(array $aRouteList)
    {
        $this->routes = $aRouteList;
    }

    /**
     * @param string $fromUnLocode
     * @param string $toUnLocode
     * @return TransitPathDto[]
     */
    public function findShortestPath(string $fromUnLocode, string $toUnLocode): array
    {
        $routes = \array_filter($this->routes, function($route) use ($fromUnLocode, $toUnLocode) {
            return $route['origin'] === $fromUnLocode && $route['destination'] === $toUnLocode;
        });

        $transitPaths = array();

        foreach($routes as $route) {
            $transitPaths[] = $this->routeToTransitPath($route);
        }

        return $transitPaths;
    }



    /**
     * @param array $route
     * @return TransitPathDto
     */
    private function routeToTransitPath(array $route): TransitPathDto
    {
        $edges = array();

        $loadDay = \rand(1,4);

        $loadTimestamp = strtotime("+$loadDay day");

        $loadTime = new \DateTime();
        $loadTime->setTimestamp($loadTimestamp);

        $elapsedDays = 0;

        $currentLocation = $route['origin'];
        $currentTime = $loadTime;

        if (!empty($route['stops'])) {
            foreach($route['stops'] as $unLocode => $duration) {
                $elapsedDays += $duration;

                $durationInterval = new \DateInterval('P' . $duration . 'DT' . \rand(1, 12) . 'H' . \rand(1, 60) . 'M');

                $currentTime->add(new \DateInterval('P1DT' . \rand(1, 12) . 'H' . \rand(1, 60) . 'M'));

                $loadTime = clone $currentTime;

                $currentTime->add($durationInterval);

                $unloadTime = clone $currentTime;

                $edge = new EdgeDto();

                $edge->setFromUnLocode($currentLocation);
                $edge->setToUnLocode($unLocode);
                $edge->setFromDate($loadTime->format(\DateTime::ATOM));
                $edge->setToDate($unloadTime->format(\DateTime::ATOM));

                $edges[] = $edge;

                $currentLocation = $unLocode;
            }
        }

        $destinationDuration = $route['duration'] - $elapsedDays;
        $durationInterval = new \DateInterval('P' . $destinationDuration . 'DT' . \rand(1, 12) . 'H' . \rand(1, 60) . 'M');

        $currentTime->add(new \DateInterval('P1DT' . \rand(1, 12) . 'H' . \rand(1, 60) . 'M'));

        $loadTime = clone $currentTime;

        $currentTime->add($durationInterval);

        $unloadTime = clone $currentTime;

        $edge = new EdgeDto();

        $edge->setFromUnLocode($currentLocation);
        $edge->setToUnLocode($route['destination']);
        $edge->setFromDate($loadTime->format(\DateTime::ATOM));
        $edge->setToDate($unloadTime->format(\DateTime::ATOM));

        $edges[] = $edge;

        $transitPath = new TransitPathDto();

        $transitPath->setEdges($edges);

        return $transitPath;
    }
}
