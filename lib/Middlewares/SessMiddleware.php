<?php

namespace Lib\Middlewares;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface		 as Response;

class SessMiddleware {
	public function __invoke(Request $request, Response $response, callable $next) {
		if (strpos($request->getUri()->getPath(), 'login') !== false) {
			file_put_contents('log.log', $request->getUri()->getPath());
			return $next($request, $response);
		} else {}

		file_put_contents('log.log', "\n" . $request->getUri()->getPath(), FILE_APPEND);
		$cookie = \Dflydev\FigCookies\Cookies::fromRequest($request);
		$cookie = FigRequestCookies::get($request, 'hash');

		$db = json_decode(file_get_contents('cookie.json'));
		$user_data = array_filter($db, function ($user_data) use ($cookie) { return $cookie->getValue() === $user_data->hash; });

		if (count($user_data)) {
			$request = $request->withAttribute('username', $user_data[0]->name);
			$response = $next($request, $response);
			$setCookie = SetCookie::create('hash')
			    ->withValue(md5($user_data[0]->name))
			    ->withExpires(time()+60)
			    ->withMaxAge(60);
			    // ->rememberForever();
			$response = FigResponseCookies::set($response, $setCookie);

			return $response;
			file_put_contents('log.log', "\nlife after life", FILE_APPEND);
		} else {
			if ($request->isXhr()) {
				return $response->withStatus(401);
			} else {
				return $response->withRedirect('/login');
			}
		}
	}
}