<?php
namespace Netdudes\Branchio;

class Directories {

    protected $baseDirectory;

    /**
     * @param $baseDirectory
     */
    public function __construct($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    public function directoryExistsForBranch($branch) {
        return is_dir($this->baseDirectory . DIRECTORY_SEPARATOR . $branch);
    }
}