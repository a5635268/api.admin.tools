<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Game;
use libs\Log;

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

    }

    public function test()
    {

    }


}