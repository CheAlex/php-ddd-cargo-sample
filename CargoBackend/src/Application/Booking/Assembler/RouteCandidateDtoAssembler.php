<?php
/*
 * This file is part of the prooph/php-ddd-cargo-sample.
 * (c) Alexander Miertsch <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 29.03.14 - 21:06
 */
declare(strict_types = 1);

namespace Codeliner\CargoBackend\Application\Booking\Assembler;

use Codeliner\CargoBackend\Application\Booking\Dto\LegDto;
use Codeliner\CargoBackend\Application\Booking\Dto\RouteCandidateDto;
use Codeliner\CargoBackend\Model\Cargo\Itinerary;
use Codeliner\CargoBackend\Model\Cargo\Leg;

/**
 * Class RouteCandidateDtoAssembler
 *
 * @package CargoBackend\API\Booking\Assembler
 * @author Alexander Miertsch <contact@prooph.de>
 */
class RouteCandidateDtoAssembler
{
    /**
     * @param Itinerary $anItinerary
     * @return RouteCandidateDto
     */
    public function toDto(Itinerary $anItinerary): RouteCandidateDto
    {
        $legs = array();

        foreach ($anItinerary->legs() as $leg) {
            $legs[] = new LegDto(
                $leg->loadLocation(),
                $leg->unloadLocation(),
                $leg->loadTime()->format(\DateTime::ATOM),
                $leg->unloadTime()->format(\DateTime::ATOM)
            );
        }

        return new RouteCandidateDto($legs);
    }

    /**
     * @param RouteCandidateDto $aRouteCandidate
     * @return Itinerary
     */
    public function toItinerary(RouteCandidateDto $aRouteCandidate): Itinerary
    {
        $legs = array();

        foreach ($aRouteCandidate->getLegs() as $legDto) {
            $legs[] = new Leg(
                $legDto->getLoadLocation(),
                $legDto->getUnloadLocation(),
                \DateTimeImmutable::createFromFormat(\DateTime::ATOM, $legDto->getLoadTime()),
                \DateTimeImmutable::createFromFormat(\DateTime::ATOM, $legDto->getUnloadTime())
            );
        }

        return new Itinerary($legs);
    }
}
