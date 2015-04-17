<?php
namespace Netdudes\Branchio;

use GitWrapper\GitException;
use GitWrapper\GitWrapper;

class Git
{

    private static $blacklistBranches = [
        '/.*HEAD.*\-\>.*/'
    ];

    /**
     * @var string
     */
    protected $remoteName;

    /**
     * @var \GitWrapper\GitWorkingCopy
     */
    protected $workingCopy;

    /**
     * @var string
     */
    private $repositoryPath;

    /**
     * @param $repositoryPath
     * @param $remoteName
     */
    public function __construct($repositoryPath, $remoteName, $sshPrivateKey = null)
    {
        $gitClient = new GitWrapper();
        if ($sshPrivateKey) {
            $gitClient = $gitClient->setPrivateKey($sshPrivateKey);
        }
        $this->workingCopy = $gitClient->workingCopy($repositoryPath);
        $this->repositoryPath = $repositoryPath;
        $this->remoteName = $remoteName;
    }

    /**
     * @param $branch
     * @param $directory
     *
     * @throws \Exception
     */
    public function cloneSite($branch, $directory)
    {
        $this->refresh();
        if (!$this->branchIsValid($branch)) {
            throw new \Exception("Invalid branch");
        }

        $gitClient = new GitWrapper();
        $workingCopy = $gitClient->cloneRepository($this->getRemoteUrl(), $directory);
        $workingCopy->checkout($this->remoteName . '/' . $branch, ['t' => true]);
    }

    /**
     * Get all the remote branches
     *
     * @return array
     */
    public function getBranches()
    {
        $branches = $this->workingCopy->getBranches()->remote();

        $branches = array_filter(
            $branches,
            function ($branch) {
                if (strpos($branch, $this->remoteName . '/') !== 0) {
                    return false;
                }

                foreach ($this::$blacklistBranches as $pattern) {
                    if (preg_match($pattern, $branch)) {
                        return false;
                    }
                }

                return $branch;
            }
        );

        return array_map(
            function($branch) {
                return implode('/', array_slice(explode('/', $branch), 1));
            },
            $branches
        );
    }

    /**
     * @param $branch
     *
     * @return bool
     */
    public function branchIsValid($branch)
    {
        return in_array($branch, $this->getBranches());
    }

    /**
     * Refresh the branches (fetch)
     */
    public function refresh()
    {
        $this->workingCopy->fetch($this->remoteName);
        $this->workingCopy->remote('prune', 'origin');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getRemoteUrl()
    {
        $remotes = $this->workingCopy->remote('-v')->getOutput();

        $remotes = array_map(
            function ($line) {
                $parts = preg_split('/\s+/', $line);

                return [$parts[0], $parts[1]];
            },
            explode("\n", $remotes)
        );

        $remotes = array_filter(
            $remotes,
            function ($line) {
                return $line[0] == $this->remoteName;
            }
        );

        $remotes = array_map(
            function ($line) {
                return $line[1];
            },
            $remotes
        );

        if (!count($remotes)) {
            throw new \Exception("No remote found");
        }

        return $remotes[0];
    }

    /**
     * @param $branch
     *
     * @return mixed
     */
    public function buildBranchReadableName($branch)
    {
        // Replace slashes with dashes
        return str_replace('/', '-', $branch);
    }
}