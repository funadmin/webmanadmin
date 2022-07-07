<?php
namespace app\backend\controller\member;

use app\backend\model\MemberLevel as MemberLevelModel;
use app\common\controller\Controller;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;
use support\View;
use support\Request;

/**
 * @ControllerAnnotation (title="会员等级")
 * Class MemberLevel
 * @package app\backend\controller\member
 */
class MemberLevel extends Controller{

    public function beforeAction(Request $request)
    {
        parent::beforeAction($request);
        $this->modelClass = new MemberLevelModel();
    }
    /**
     * @NodeAnnotation (title="添加")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(){

        if (request()->isPost()) {
            $post = request()->post();
            $rule = [
                'name|等级名称' => [
                    'require' => 'require',
                    'max'     => '255',
                    'unique'  => 'member_level',
                ],
                'description|描述' => [
                    'max' => '255',
                ],
            ];
            $this->validate($post, $rule);
            try {
                $save = $this->modelClass->save($post);
            } catch (\Exception $e) {
                return $this->error(lang('Save failed'));
            }
            if($save) return $this->success(lang('Save success')) ;
            return $this->error(lang('Save failed'));
        }
        $view = [
            'formData' => '',
            'title' => lang('Add'),
        ];
        return fetch('',$view);
    }

}