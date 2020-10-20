<?php

declare(strict_types=1);

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

trait BehatProphecyTrait
{
    private ?Prophet $prophet = null;

    protected function prophesize(?string $classOrInterface = null): ObjectProphecy
    {
        if (null === $this->prophet) {
            $this->prophet = new Prophet();
        }

        return $this->prophet->prophesize($classOrInterface);
    }

    /**
     * @AfterScenario
     */
    public function verifyProphecyDoubles(): void
    {
        if (null === $this->prophet) {
            return;
        }

        $this->prophet->checkPredictions();
    }
}
