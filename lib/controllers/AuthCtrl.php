<?php
namespace Lib\Controllers;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;

use \Slim\Container as Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AuthCtrl {
	protected $container;
	protected $view;

	public function __construct(Container $container) {
	    $this->container = $container;
	    $this->view = $container->get('view');
	}

	public function login(Request $request, Response $response): Response {
	    return $this->view->render($response, 'login.html', []);
	}

	public function checkLogin (Request $request, Response $response):Response {
		$body = $request->getParsedBody();
		$setCookie = SetCookie::create('hash')
			->withValue(md5($body['username']))
			->withExpires(time()+60)
			->withMaxAge(60);
			// ->rememberForever();
		$response = FigResponseCookies::set($response, $setCookie);
		return $response->withRedirect('/');
		// if ($body['username'] === 'user' && $body['password'] === 'pass') {
		// } else {
		// 	return $response->withStatus(401);
		// }
	}
}