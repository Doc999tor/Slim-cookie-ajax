<?php
namespace Lib\Controllers;

use Dflydev\FigCookies\FigRequestCookies;
use \Slim\Container as Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class SessCtrl {
    protected $container;
    protected $view;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->view = $container->get('view');
    }

    public function index(Request $request, Response $response): Response {
        $cookie = FigRequestCookies::get($request, 'hash');
        if ($cookie->getValue() !== 'ee11cbb19052e40b07aac0ca060c23ee') {
            return $response->withRedirect('/login');
        } else {
            return $this->view->render($response, 'home.html', [
                "cookie" => $cookie->getValue(),
                "attr" => $request->getAttribute('username'),
            ]);
        }
    }
    public function ajax(Request $request, Response $response): Response {
        $body = $response->getBody();
        $cookie = FigRequestCookies::get($request, 'theme')->getValue();
        if ($cookie) {
            $body->write($cookie);
            return $response;
        } else {
            return $response->withStatus(401);
        }
    }
}