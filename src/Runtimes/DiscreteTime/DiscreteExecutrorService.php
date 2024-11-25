<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes\DiscreteTime;

use Zodimo\Actor\Actor;
use Zodimo\Actor\Runtimes\ExecutorService;

class DiscreteExecutrorService implements ExecutorService
{
    /**
     * @var array<Actor<mixed>>
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

    public function execute(Actor $actor): void
    {
        $this->actors[] = $actor;
    }

    /**
     * @param callable(int):bool $until
     */
    public function run(callable $until): void
    {
        // step until.... all inboxes empty?

        // address must be a client to talk send messages to where ever the mailbox and execution lives

        // LOOP UNTIL not work left to do  ??

        while ($until($this->execStepper->getCurrentStep())) {
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
        }
    }
}
