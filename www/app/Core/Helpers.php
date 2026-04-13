<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

if (!function_exists('request')) {
    /**
     * Get the global Illuminate Request instance.
     */
    function request()
    {
        static $request = null;
        if (!$request) {
            $request = Request::capture();
        }
        return $request;
    }
}

if (!function_exists('response')) {
    /**
     * Return a new Illuminate Response instance or JsonResponse.
     * 
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return Response|JsonResponse|RedirectResponse
     */
    function response($content = '', $status = 200, array $headers = [])
    {
        // Add custom helpers to the response
        return new class($content, $status, $headers) {
            public $content;
            public $status;
            public $headers;

            public function __construct($content, $status, $headers) {
                $this->content = $content;
                $this->status = $status;
                $this->headers = $headers;
            }

            public function send() {
                $response = new Response($this->content, $this->status, $this->headers);
                $response->send();
                exit;
            }

            public function json($data, $status = 200, array $headers = []) {
                $response = new JsonResponse($data, $status, $headers);
                $response->send();
                exit;
            }

            public function redirect($url, $status = 302, array $headers = []) {
                $response = new RedirectResponse($url, $status, $headers);
                $response->send();
                exit;
            }
            
            // Allow string casting to behave like standard Illuminate Response
            public function __toString() {
                return (new Response($this->content, $this->status, $this->headers))->__toString();
            }
        };
    }
}

if (!function_exists('redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int  $status
     * @param  array  $headers
     * @return RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [])
    {
        $response = new RedirectResponse($to, $status, $headers);
        return $response;
    }
}

if (!function_exists('session')) {
    /**
     * Get the global Session instance wrapping PHP native sessions safely.
     */
    function session()
    {
        static $session = null;
        if (!$session) {
            $session = new Session();
            // Symfony Session acts cleanly on $_SESSION for immediate OOP use.
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        return clone new class($session) {
            private $session;
            public function __construct($session) { $this->session = $session; }
            public function put($key, $value) { $this->session->set($key, $value); }
            public function get($key, $default = null) { return $this->session->get($key, $default); }
            public function has($key) { return $this->session->has($key); }
            public function forget($key) { $this->session->remove($key); }
            public function all() { return $this->session->all(); }
        };
    }
}
