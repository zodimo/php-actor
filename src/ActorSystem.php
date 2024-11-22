<?php

declare(strict_types=1);

namespace Zodimo\Actor;

use Zodimo\Actor\Runtimes\ExecutorService;
use Zodimo\Actor\Runtimes\MailboxFactory;

/**
 * `THE` Actor System.
 */
class ActorSystem implements ActorSystemInterface
{
    /**
     * @var \SplObjectStorage<Address<mixed>,Mailbox<mixed,mixed,mixed>>
     */
    private \SplObjectStorage $mailboxRegistry;

    /**
     * @param MailboxFactory<mixed,mixed> $mailboxFactory
     */
    public function __construct(private ExecutorService $executorService, private MailboxFactory $mailboxFactory)
    {
        $this->mailboxRegistry = new \SplObjectStorage();
    }

    /**
     * @param MailboxFactory<mixed,mixed> $mailboxFactory
     */
    public static function create(ExecutorService $executorService, MailboxFactory $mailboxFactory): ActorSystem
    {
        return new self($executorService, $mailboxFactory);
    }

    /**
     * @template _MESSAGE
     *
     * @param class-string<_MESSAGE>                                         $messageClass
     * @param callable(AddressInterface<mixed>):BehaviourInterface<_MESSAGE> $constructor
     *
     * @return AddressInterface<_MESSAGE>
     */
    public function actorOf(string $messageClass, callable $constructor): AddressInterface
    {
        $mailbox = $this->mailboxFactory->createMailbox($messageClass);

        $actorAddress = Address::create($mailbox, $constructor);
        $this->mailboxRegistry->attach($actorAddress, $mailbox);
        // wire up the executor to the actor::run ....
        $this->executorService->execute($actorAddress);

        return $actorAddress;
    }
}
