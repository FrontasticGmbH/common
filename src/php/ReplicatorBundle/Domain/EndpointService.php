<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

class EndpointService
{
    private $targets = [];

    private $sources = [];

    public function addReplicationSource(string $channel, Source $source): void
    {
        if (isset($this->sources[$channel])) {
            throw new \RuntimeException("A source for $channel already exists.");
        }

        $this->sources[$channel] = $source;
    }

    public function getReplicationSource(string $channel)
    {
        if (!isset($this->sources[$channel])) {
            throw new \RuntimeException("A source for $channel does not exists.");
        }

        return $this->sources[$channel];
    }

    public function addReplicationTarget(string $channel, Target $target): void
    {
        if (isset($this->targets[$channel])) {
            throw new \RuntimeException("A target for $channel already exists.");
        }

        $this->targets[$channel] = $target;
    }

    public function getReplicationTarget(string $channel)
    {
        if (!isset($this->targets[$channel])) {
            throw new \RuntimeException("A target for $channel does not exists.");
        }

        return $this->targets[$channel];
    }

    public function dispatch(Command $command): Result
    {
        $result = new Result();

        switch ($command->command) {
            case 'lastUpdate':
                if (!isset($this->targets[$command->channel])) {
                    throw new \OutOfBoundsException("No target available for channel {$command->channel}.");
                }

                $result->payload = [
                    'revision' => $this->targets[$command->channel]->lastUpdate()
                ];
                break;

            case 'updates':
                if (!isset($this->sources[$command->channel])) {
                    throw new \OutOfBoundsException("No source available for channel {$command->channel}.");
                }

                $result->payload = [
                    'updates' => $this->sources[$command->channel]->updates(
                        $command->payload['since'],
                        $command->payload['count'] ?? 0
                    ),
                ];
                break;

            case 'replicate':
                if (!isset($this->targets[$command->channel])) {
                    throw new \OutOfBoundsException("No target available for channel {$command->channel}.");
                }

                $this->targets[$command->channel]->replicate(
                    $command->payload['updates']
                );
                break;

            default:
                throw new \RuntimeException("Unknown command {$command->command}.");
        }

        return $result;
    }
}
