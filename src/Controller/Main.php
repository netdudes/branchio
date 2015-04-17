<?php

namespace Netdudes\Branchio\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Main
{

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function listAction(Request $request, Application $app)
    {
        $branches = array_map(
            [$app['git'], 'buildBranchReadableName'],
            $app['git']->getBranches()
        );

        $table = array_map(
            function ($branch) use ($app) {
                return [
                    'branch' => $branch,
                    'directory_exists' => $app['sites']->siteExistsForBranch($branch),
                    'url' => $app['url_builder']->buildUrlForBranch($branch)
                ];
            },
            $branches
        );

        return $app['twig']->render(
            'list.twig',
            [
                'table' => $table,
            ]
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return RedirectResponse
     */
    public function refreshAction(Request $request, Application $app)
    {
        $app['git']->getRemoteUrl();

        return $app->redirect('/');
    }
}