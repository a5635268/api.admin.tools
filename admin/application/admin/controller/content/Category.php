<?php

namespace app\admin\controller\content;

use app\common\controller\Backend;
use fast\Tree;

/**
 * 分类管理
 *
 * @icon fa fa-circle-o
 */
class Category extends Backend
{

    /**
     * Category模型对象
     * @var \app\admin\model\content\Category
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\content\Category;
        $this->getCategorydata();
    }

    private function getCategorydata()
    {
        $tree = Tree::instance();
        $fields = '`category_id` id, category_id, `pid`, `name`, 
        `py_name`, `icon`,label, `weigh`, `status`';
        $order = 'weigh desc,category_id desc';
        $data = $this->model
            ->field($fields)
            ->order($order)
            ->select();
        $tree->init(collection($data)->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [0 => ['name' => __('None')]];

        // 获得二级分类
        $where['pid'] = 0;
        $pids = $this->model
            ->where($where)
            ->column('category_id');
        $data = $this->model
            ->where($where)
            ->whereOr('pid','in',$pids)
            ->field($fields)
            ->order($order)
            ->select();
        $data = collection($data)->toArray();
        $tree->init($data, 'pid');
        $list = $tree->getTreeList($tree->getTreeArray(0), 'name');
        foreach ($list as $k => $v)
        {
            $categorydata[$v['id']] = $v;
        }
        $this->view->assign("parentList", $categorydata);
        return $categorydata;
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            $search = $this->request->request("search");

            //构造父类select列表选项数据
            $list = [];

            foreach ($this->categorylist as $k => $v)
            {
                if ($search) {
                    if (stripos($v['name'], $search) !== false || stripos($v['py_name'], $search) !== false)
                    {
                        $list[] = $v;
                    }
                } else {
                    $list[] = $v;
                }
            }

            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function del($ids = ""){
        $res = $this->model
            ->where('pid','in',$ids)
            ->find();
        if($res){
            $this->error("请先删除子分类");
        }

        #todo:判断该分类下有没有机构
        parent::del($ids);
    }


    public function seleted()
    {
        $categoryId = input('category_id/d' , 0);
        if (empty($categoryId)) {
            $data = $this->model
                ->where('pid',0)
                ->field('category_id value,name')
                ->select();
        }else{
            $data = $this->model
                ->where('pid',$categoryId)
                ->field('category_id value,name')
                ->select();
        }

        $this->success('', null, $data);
    }

    public function selectpage()
    {
        $categoryId = input('category_id');
        //当前页
        $page = $this->request->request("pageNumber");
        //分页大小
        $pagesize = $this->request->request("pageSize");
        $name = $this->request->request("name/s");
        // 默认搜索
        $searchValue = $this->request->request("searchValue/s");
        if(empty($categoryId)){
            if($searchValue){
                $list = $this->model
                    ->where('category_id','in', $searchValue)
                    ->field('category_id id,name')
                    ->select();
                $total = $this->model
                    ->where('category_id','in', $searchValue)
                    ->count();
                return json(['list' => $list , 'total' => $total]);
            }
            return false;
        }
        $list = [];
        $where = ['pid' => $categoryId];
        $name && $where['name'] = ['like',"%{$name}%"];
        $total = $this->model
            ->where($where)
            ->count();
        if ($total > 0) {
            $list = $this->model
                ->where($where)
                ->order('category_id desc')
                ->page($page , $pagesize)
                ->field('category_id id,name')
                ->select();
        }
        //这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
        return json(['list' => $list , 'total' => $total]);
    }
}
