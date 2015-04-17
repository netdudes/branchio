<?php
namespace Netdudes\Branchio;

use GitWrapper\GitWrapper;

class Sites
{

    protected $baseDirectory;

    /**
     * @var Git
     */
    private $git;

    /**
     * @param           $baseDirectory
     * @param Git $git
     */
    public function __construct($baseDirectory, Git $git = null)
    {
        $this->baseDirectory = $baseDirectory;
        $this->git = $git;
    }

    /**
     * @param $branch
     *
     * @return bool
     */
    public function siteExistsForBranch($branch)
    {
        return is_dir($this->getSiteDirectoryForBranch($branch));
    }

    /**
     * @param $branch
     *
     * @throws \Exception
     */
    public function updateSite($branch)
    {
        if (!$this->siteExistsForBranch($branch)) {
            throw new \Exception("Site is not initialised");
        }

        $directory = $this->baseDirectory . DIRECTORY_SEPARATOR . $this->git->buildBranchReadableName($branch);
        exec($directory . DIRECTORY_SEPARATOR . 'scripts/update_and_reset.sh');
    }

    /**
     * @param $branch
     *
     * @return string
     */
    public function getSiteDirectoryForBranch($branch)
    {
        return $this->baseDirectory . DIRECTORY_SEPARATOR . $branch;
    }

    /**
     * @param $branch
     *
     * @throws \Exception
     */
    public function buildSite($branch)
    {
        if ($this->siteExistsForBranch($branch)) {
            throw new \Exception("Branch has a site already set up");
        }

        $directory = $this->baseDirectory . DIRECTORY_SEPARATOR . $this->git->buildBranchReadableName($branch);
        mkdir($directory);
        $this->git->cloneSite($branch, $directory);
        exec($directory . DIRECTORY_SEPARATOR . 'scripts/update_and_reset.sh');
    }
}