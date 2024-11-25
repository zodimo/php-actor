<?php

declare(strict_types=1);

namespace Zodimo\Actor;

use Zodimo\Actor\Messages\AddressMessage;
use Zodimo\Actor\Runtimes\ExecutorService;
use Zodimo\Actor\Runtimes\MailboxFactory;

/**
 * `THE` Actor System.
 */
class ActorSystem implements ActorSystemInterface
{
    /**
     * @var \SplObjectStorage<ActrorRefInterface<mixed>,Mailbox<mixed,mixed,mixed>>
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
     * @param class-string<_MESSAGE>                                           $messageClass
     * @param callable(ActrorRefInterface<mixed>):BehaviourInterface<_MESSAGE> $constructor
     *
     * @return ActrorRefInterface<_MESSAGE>
     */
    public function actorOf(string $messageClass, callable $constructor): ActrorRefInterface
    {
        $mailbox = $this->mailboxFactory->createMailbox($messageClass);

        $actor = Actor::create($mailbox, $constructor);
        $ref = ActorRef::create(fn ($message) => $actor->tell($message));
        $this->mailboxRegistry->attach($ref, $mailbox);
        $addressMessage = AddressMessage::create($ref);
        $actor->tell($addressMessage);
        // wire up the executor to the actor::run ....
        $this->executorService->execute($actor);

        return $ref;
    }
}
