<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes\DiscreteTime;

use Zodimo\Actor\Address;
use Zodimo\Actor\Runtimes\ExecutorService;

class DiscreteExecutrorService implements ExecutorService
{
    /**
     * @var array<Address<mixed>>
     */
    private array $actors;

    public function __construct(private ExecutionStepper $execStepper)
    {
        $this->actors = [];
    }

    public static function create(ExecutionStepper $execStepper): DiscreteExecutrorService
    {
        return new self($execStepper);
    }

    public function execute(Address $address): void
    {
        $this->actors[] = $address;
    }

    public function run(int $steps = 100): void
    {
        // step until.... all inboxes empty?

        // address must be a client to talk send messages to where ever the mailbox and execution lives

        $counter = $steps;
        while ($counter >= 0) {
            foreach ($this->actors as $actor) {
                $result = $actor->run();
                if ($result->isFailure()) {
                    // supervisor should handler this error
                    // for now.. abort..
                    $result->match(
                        function ($_) {},
                        function ($error) {
                            throw new \RuntimeException('Actor has error: '.(string) $error);
                        }
                    );
                }
            }
            $this->execStepper->tick();
            --$counter;
        }
    }
}
