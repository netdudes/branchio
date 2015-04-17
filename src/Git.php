<?php
namespace Netdudes\Branchio;

use GitWrapper\GitWrapper;

class Git {

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
    public function __construct($repositoryPath, $remoteName)
    {
        $gitClient = new GitWrapper();
        $this->workingCopy = $gitClient->workingCopy($repositoryPath);
        $this->repositoryPath = $repositoryPath;
        $this->remoteName = $remoteName;
    }

    /**
     * Get all the remote branches
     * @return array
     */
    public function getBranches()
    {
        $branches = $this->workingCopy->getBranches()->remote();
        $branchesInThisRemote = array_filter(
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
                // Remove the origin/ part
                $branch = implode('/', array_slice(explode('/', $branch), 1));
                // Replace slashes with dashes
                $branch = str_replace('/', '-', $branch);
                return $branch;
            },
            $branchesInThisRemote
        );
    }

    /**
     * Refresh the branches (fetch)
     */
    public function refresh()
    {
        $this->workingCopy->fetch($this->remoteName);
        $this->workingCopy->remote('prune', 'origin');
    }
}