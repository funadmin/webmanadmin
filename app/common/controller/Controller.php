<?php

namespace app\common\controller;

use app\common\model\Languages;
use app\common\traits\Curd;
use app\common\traits\Jump;
use support\Request;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Cookie;
use think\helper\Str;
use think\Template;
use think\Validate;
use Gregwar\Captcha\CaptchaBuilder;
use Webman\View;

class Controller
{
    use Curd;
    use Jump;

    protected $app;
    protected $controller;
    protected $action;
    protected $route;
    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 主键 id
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * @var
     * 模型
     */
    protected $modelClass;
    /**
     * @var
     * 页面大小
     */
    protected $pageSize;
    /**
     * @var
     * 页数
     */
    protected $page;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';
    /**
     * 下拉选项条件
     * @var string
     */

    protected $selectMap = [];

    protected $allowModifyFields = [
        'status',
        'sort',
        'title',
        'auth_verify',
    ];
    /**
     * 是否是关联查询
     */
    protected $relationSearch = false;

    /**
     * 关联join搜索
     * @var array
     */
    protected $joinSearch = [];

    protected $request;

    /**
     * 该方法会在请求后调用
     */
    public function afterAction(Request $request)
    {

        // 如果想串改请求结果，可以直接返回一个新的Response对象
        // return response('afterAction');
    }

    public function beforeAction(Request $request)
    {
        list($this->app,$this->controller,$this->action,$this->route,$this->url) = getNodeInfo();
        //过滤参数
        $this->pageSize = request()->input('limit', 15);
        $this->page = request()->input('page', 1);
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        try {
            $this->checkToken();
            if (is_array($validate)) {
                $v = new Validate();
                $v->rule($validate);
            } else {
                if (strpos($validate, '.')) {
                    // 支持场景
                    list($validate, $scene) = explode('.', $validate);
                }
                $class = false !== strpos($validate, '\\') ? $validate : $this->parseClass('validate', $validate);
                $v     = new $class();
                if (!empty($scene)) {
                    $v->scene($scene);
                }
            }
            $v->message($message);
            // 是否批量验证
            if ($batch || $this->batchValidate) {
                $v->batch(true);
            }
            $v->failException(true)->check($data);

        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }
        return true;

    }

    protected function parseClass(string $layer, string $name): string
    {
        $name  = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = Str::studly(array_pop($array));
        $path  = $array ? implode('\\', $array) . '\\' : '';

        return $this->namespace . '\\' . $layer . '\\' . $path . $class;
    }

    /**
     * 检测token 并刷新
     *
     */
    protected function checkToken()
    {
        $check = request()->input('__token__');
        if (false === $check) {
            $this->error(lang('Token verify error'));
        }
    }

    /**
     * @return void
     */
    //自动加载语言
    protected function loadlang($name, $addon='')
    {
        return loadLang($name);
    }

    public function verify(Request $request)
    {
        // 初始化验证码类
        $builder = new CaptchaBuilder;
        // 生成验证码
        $builder->build();
        // 将验证码的值存储到session中
        request()->session()->set('captcha', strtolower($builder->getPhrase()));
        // 获得验证码图片二进制数据
        $img_content = $builder->get();
        // 输出验证码二进制数据
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 检查验证码
     */
    public function verifyCheck()
    {
        // 获取post请求中的captcha字段
        $captcha = request()->input('captcha');
        // 对比session中的captcha值
        if (strtolower($captcha) !== request()->session()->get('captcha')) {
            return false;
        }
        return true;
    }

    public function enlang()
    {
        $lang = request()->get('lang');
        $language = Languages::where('name',$lang)->find();
        if(!$language) return $this->error(lang('please check language config'));
        if(strtolower($lang)=='zh-cn' || !$lang){
            $lang = 'zh-cn';
        }
        locale($lang);
        request()->session()->set('lang',$lang);
        Cache::clear();
        return $this->success(lang('Change Success'));
    }
}