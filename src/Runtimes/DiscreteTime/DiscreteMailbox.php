<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes\DiscreteTime;

use Zodimo\Actor\Mailbox;
use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;

/**
 * @template MESSAGE
 *
 * @template-implements Mailbox<MESSAGE,mixed,mixed>
 */
class DiscreteMailbox implements Mailbox
{
    /**
     * Summary of inbox.
     *
     * @var \SplQueue<MESSAGE>
     */
    private \SplQueue $inbox;

    /**
     * @var \SplQueue<MESSAGE>
     */
    private \SplQueue $offeredMessages;

    private int $currentStep;

    /**
     * @param class-string<MESSAGE> $messageClass
     *
     * @phpstan-ignore property.onlyWritten
     */
    private function __construct(private ExecutionStepper $execStepper, private string $messageClass)
    {
        $this->inbox = new \SplQueue();
        $this->offeredMessages = new \SplQueue();

        $this->currentStep = $this->execStepper->getCurrentStep();
    }

    /**
     *  @template _MESSAGE
     *
     * @param class-string<_MESSAGE> $messageClass
     *
     * @return DiscreteMailbox<_MESSAGE>
     */
    public static function create(ExecutionStepper $execStepper, string $messageClass): DiscreteMailbox
    {
        return new self($execStepper, $messageClass);
    }

    /**
     * @param MESSAGE $message
     *
     * @return IOMonad<null,mixed>
     */
    public function offer($message): IOMonad
    {
        $this->offeredMessages->enqueue($message);

        return IOMonad::pure(null);
    }

    /**
     * @return IOMonad<Option<MESSAGE>,mixed>
     */
    public function take(): IOMonad
    {
        while (true) {
            // drop messages of the wrong type
            if (!$this->inbox->isEmpty()) {
                $message = $this->inbox->dequeue();

                // even dropped my AccountMessage
                // if ($message instanceof $this->messageClass) {
                //     /**
                //      * @var MESSAGE $message
                //      */
                //     return IOMonad::pure(Option::some($message));
                // }
                return IOMonad::pure(Option::some($message));
            }

            break;
        }

        $this->rotateOnStepChange();

        // @phpstan-ignore return.type
        return IOMonad::pure(Option::none());
    }

    private function canRotate(): bool
    {
        return $this->inbox->isEmpty();
    }

    private function rotateOnStepChange(): void
    {
        if ($this->currentStep !== $this->execStepper->getCurrentStep()) {
            if (!$this->canRotate()) {
                throw new \RuntimeException('Cannot rotate non-empty inbox');
            }
            $currentInbox = $this->inbox;

            $this->inbox = $this->offeredMessages;
            $this->offeredMessages = $currentInbox;
            $this->currentStep = $this->execStepper->getCurrentStep();
        }
    }
}
