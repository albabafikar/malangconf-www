<?php
/**
 * Routes
 *
 * development Environment
 */
namespace MalangPhp\Site\Conf;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;
use Slim\Http\Response;

if (!isset($this) || ! $this instanceof App) {
    return;
}

/**
 * @var \Slim\App
 */
$slim = $this->slim();

/**
 * Route Home
 *
 * match URL of /
 */
$slim->any('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $this->view->render($response, 'home.php');
});

/**
 * Route Api
 *
 * match URL of /api(/.*)?
 */
$slim->any(
    '/api[/[{segments: .*}]]',
    function (ServerRequestInterface $request, Response $response, $segments) {
        /**
         * Returning JSON Example
         */
        return $response->withJson(
            [
                'status' => 'ok',
                'param' => $segments
            ]
        );
    }
);

/**
 * Route Events
 *
 * match URL of /event(/.*)?
 */
$slim->any(
    '/event[/[{segments: .*}]]',
    function (ServerRequestInterface $request, ResponseInterface $response, $segments) {
        if (empty($segments['segments'])) {
            return $this->view->render($response, 'archive-event.php');
        }
        return $this->view->render($response, 'event.php');

        /**
         * not found example
         * <code>
         *      throw new NotFoundException($request, $response);
         * </code>
         * @uses NotFoundException
         */
    }
);

/**
 * Route News
 *
 * match URL of /news(/.*)?
 */
$slim->any(
    '/news[/[{segments: .*}]]',
    function (ServerRequestInterface $request, ResponseInterface $response, $segments) {
        if (empty($segments['segments'])) {
            return $this->view->render($response, 'archive-news.php');
        }
        return $this->view->render($response, 'news.php');
    }
);

/**
 * Route Default
 *
 * match URL of /(.+)
 */
$slim->any(
    '/{params: .+}',
    function (ServerRequestInterface $request, ResponseInterface $response, $segments) {
        throw new SlimException($request, $response);
    }
);
