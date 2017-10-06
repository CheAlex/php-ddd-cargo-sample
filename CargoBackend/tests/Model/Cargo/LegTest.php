<?php
/*
 * This file is part of the prooph/php-ddd-cargo-sample package.
 * (c) Alexander Miertsch <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CodelinerTest\CargoBackend\Domain\Model\Cargo;

use Codeliner\CargoBackend\Model\Cargo\Leg;
use Codeliner\CargoBackend\Model\Voyage\Voyage;
use Codeliner\CargoBackend\Model\Voyage\VoyageNumber;
use CodelinerTest\CargoBackend\TestCase;

/**
 * Class LegTest
 * 
 * @author Alexander Miertsch <contact@prooph.de>
 */
class LegTest extends TestCase
{
    /**
     * @var Leg
     */
    private $leg;

    public function setUp(): void
    {

        $this->leg = new Leg(
            'Hongkong',
            'Hamburg',
            new \DateTimeImmutable('2014-01-20 10:00:00'),
            new \DateTimeImmutable('2014-02-02 18:00:00')
        );
    }

    /**
     * @test
     */
    public function it_has_a_load_location(): void
    {
        $this->assertEquals('Hongkong', $this->leg->loadLocation());
    }

    /**
     * @test
     */
    public function it_has_an_unload_location(): void
    {
        $this->assertEquals('Hamburg', $this->leg->unloadLocation());
    }

    /**
     * @test
     */
    public function it_is_same_value_as_leg_with_same_properties(): void
    {
        $sameLeg = new Leg(
            'Hongkong',
            'Hamburg',
            new \DateTimeImmutable('2014-01-20 10:00:00'),
            new \DateTimeImmutable('2014-02-02 18:00:00')
        );

        $this->assertTrue($this->leg->sameValueAs($sameLeg));
    }
}
