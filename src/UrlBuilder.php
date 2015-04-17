<?php
namespace Netdudes\Branchio;

class UrlBuilder {

    protected $urlPattern;

    /**
     * @param $urlPattern
     */
    public function __construct($urlPattern)
    {
        $this->urlPattern = $urlPattern;

    }

    public function buildUrlForBranch($branch) {
        return '//' . str_replace('{branch}', $branch, $this->urlPattern);
    }
}