<?php

namespace app\core;

use app\controller\MessageController;

class RouterCore
{
    private $uri;
    private $method;
    private $getArr = [];

    public function __construct()
    {
        $this->initialize();
        require_once('../app/config/router.php');
        $this->execute();
    }

    private function initialize()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if(strpos($uri, '?'))
            $uri = mb_substr($uri, 0, strpos($uri, '?'));

        $ex = explode('/', $uri);

        $uri = $this->normalizerURI($ex);

        for ($i=0; $i < UNSET_URI_COUNT ; $i++) { 
            unset($uri[$i]);
        }

        $this->uri = implode('/', $this->normalizerURI($uri));
        if (DEBUG_URI)
        dd($this->uri);
    }

    private function get($router, $call)
    {
        $this->getArr[] = [
            'router' => $router,
            'call' => $call
        ];
    }

    private function execute()
    {
        switch($this->method){
            case 'GET':
                $this->executeGet();
                break;
            case 'POST':
                # code...
                break;
        }
    }

    private function executeGet()
    {
        foreach ($this->getArr as $get) {
            $r = substr($get['router'], 1);
            
            if (substr($r, 0, -1) == '/') {
                $r = substr($r, 0, -1);
                dd($r);
            }
            if ($r == $this->uri) {
                if (is_callable($get['call'])) {
                    $get['call']();
                break;
                } else {
                    $this->executeController($get['call']);
                }
            }
        }
    }

    private function executeController($get)
    {
        $ex = explode('@', $get);
        
        if (!isset($ex[0]) || !isset($ex[1])) {
            (new MessageController)->message('Dados inválidos','Controller ou método não encontrado: ' . $get, 404);
            return;
        }

        $cont = 'app\\controller\\' . $ex[0];
        if (!class_exists($cont)) {
            (new MessageController)->message('Dados inválidos','Controller não encontrado: ' . $get, 404);
            return;
        }

        if (!method_exists($cont, $ex[1])) {
            (new MessageController)->message('Dados inválidos','Método não encontrado: ' . $get, 404);
            return;
        }

        call_user_func_array([
            new $cont,
            $ex[1]
        ], []);
        // TesteController@seta
    }

    private function normalizerURI($arr)
    {
        return array_values(array_filter($arr));
    }
}