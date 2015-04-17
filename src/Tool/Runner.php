<?php
namespace Netdudes\Branchio\Tool;

class Runner
{

    protected $user;

    protected $command;

    public function __construct()
    {
        $this->command = __DIR__ . '/../../management.php';
        $this->user = fileowner($this->command);
    }

    private function buildCommand(array $arguments)
    {
        return "sudo -s {$this->user} {$this->command} " . implode(' ', $arguments);
    }

    public function refreshSite($branch)
    {
        exec($this->buildCommand('refresh', ['site' => $branch]));
    }
}