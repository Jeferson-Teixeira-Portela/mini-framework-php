<?php

$this->get('/', function () {
    (new \app\controller\TesteController)->index();
});

$this->get('/about/test', function () {
    echo 'Estou na abount test!';
});

$this->get('/categoria', 'MyController@method');