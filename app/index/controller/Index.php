<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Game;

class Index extends Base
{

    protected $game;

    public function __construct(Game $game)
    {
        parent::__construct();
        $this->game = $game;
    }

    public function index()
    {
        echo 2222;
    }

    public function hello()
    {
        dump($this->app->request);
        die;
    }

    public function test()
    {

    }


}