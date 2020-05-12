<?php

namespace app\admin\controller\content;

use app\admin\model\content\City;
use app\common\controller\Backend;
use Curl\Curl;
use fast\Pinyin;
use libs\Lbscloud;
use think\Url;

/**
 * 机构管理
 *
 * @icon fa fa-circle-o
 */
class Organization extends Backend
{

    /**
     * Organization模型对象
     * @var \app\admin\model\content\Organization
     */
    protected $model = null;

    protected $searchFields = 'name';
    protected $modelValidate = true;
    protected $modelSceneValidate = true;


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\content\Organization;

    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

            list(, $sort, $order, $offset, $limit, $whereRaw) = $this->buildparams();

            $isSearchCid = 0;
            $cid = 0;
            $whereRaw = array_filter($whereRaw,function($item) use(&$isSearchCid, &$cid){
                 if($item[0] === 'category_data'){
                     $isSearchCid = 1;
                     $cid = $item[2];
                     return false;
                 }
                 return true;
            });
            $whereRaw = function ($query) use ($whereRaw) {
                foreach ($whereRaw as $k => $v) {
                    if (is_array($v)) {
                        call_user_func_array([$query, 'where'], $v);
                    } else {
                        $query->where($v);
                    }
                }
            };

            $searchCid = $isSearchCid ? "find_in_set( {$cid}, category_ids )" : "";
            $total = $this->model
                ->where($whereRaw)
                ->where($searchCid)
                ->order($sort, $order)
                ->count();

            $fields = 'org_id, org_id id, city_code,city_code city_name, 
            category_ids, category_ids category_data, name, py_name,
            cover, logo,bak_url, status, weigh, create_time';

            $list = $this->model
                ->where($whereRaw)
                ->where($searchCid)
                ->field($fields)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    private function getCityData($areaCode)
    {
        $area = City::where('city_code',$areaCode)
            ->field('city_code,city_name,parent_city_code')
            ->find();
        $returns = [
            'area' => [
                'value' => $area['city_code'],
                'name'  => $area['city_name']
            ]
        ];
        $city = City::where('city_code',$area['parent_city_code'])
            ->field('city_code,city_name,parent_city_code')
            ->find();
        $returns['city'] = [
            'value' => $city['city_code'],
            'name' => $city['city_name'],
        ];
        $province = City::where('city_code',$city['parent_city_code'])
            ->field('city_code,city_name,parent_city_code')
            ->find();
        $returns['province'] = [
            'value' => $province['city_code'],
            'name' => $province['city_name'],
        ];
        $this->view->assign("city_list", $returns);
    }

    private function processCategoryData(&$params)
    {
        $categoryId3 = array_filter($params['category_id_3']);
        $categoryIds = [];
        foreach ($categoryId3 as $item){
            $item = explode(',',$item);
            $categoryIds = array_merge($categoryIds,$item);
        }
        if(empty($categoryIds)){
            $this->error('缺少分类');
        }
        $params['category_ids'] = implode(',',$categoryIds);
        $categoryData = [];
        $category3Data = array_filter($params['category_id_3']);
        foreach ($category3Data as $k=>$v){
            $categoryData[$k] = [
                '1' => $params['category_id_1'][$k],
                '2' => $params['category_id_2'][$k],
                '3' => $v
            ];
        }
        $params['category_json'] = json_encode($categoryData);
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $this->processCategoryData($params);
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        $cityCode = $row['city_code'];
        $this->getCityData($cityCode);
        $categoryData = json_decode($row['category_json'],true);

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    $this->processCategoryData($params);
                    $result = $row->allowField(true)->save($params);

                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign('category_data',$categoryData);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
