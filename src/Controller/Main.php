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
        $table = array_map(
            function ($branch) use ($app) {
                return [
                    'branch' => $branch,
                    'directory_exists' => $app['directories']->directoryExistsForBranch($branch),
                    'url' => $app['url_builder']->buildUrlForBranch($branch)
                ];
            },
            $app['git']->getBranches()
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
        $app['git']->refresh();
        return $app->redirect('/');
    }
}