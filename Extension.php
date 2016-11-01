<?php
namespace yii\safe;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Created by PhpStorm.
 * User: blake_sha
 * Date: 2016/6/29
 * Time: 14:11
 * 登录安全机制的扩展
 */
class Extension extends Model
{
    //验证的方法
    public $method = ['checkNumber'];
    //缓存名称
    public $cacheName = 'cache';
    //最大错误次数
    public $maxNumber = 10;
    //缓存时间 单位：分钟
    public $cacheTime = 5;
    private $_cache;

    /**
     * 出始化策略类
     */
    public function init()
    {
        parent::init();
        //获取缓存
        $cacheName = $this->cacheName;
        if(!\Yii::$app->$cacheName){
             throw new InvalidConfigException("Can't get {$this->cacheName} cache check config");
        }
        $this->_cache = \Yii::$app->$cacheName;
        $this->maxNumber = intval($this->maxNumber);
        //分转秒
        $this->cacheTime = $this->cacheTime * 60;




    }

    public function run($param = null)
    {
        foreach ($this->method as $valueFunction) {
            if(!method_exists($this,$valueFunction)){
                throw new InvalidConfigException("Call undefined function {$valueFunction} check method param");
            }
            if (!$this->$valueFunction($param)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 根据登录次数验证
     * @return bool
     */
    public function checkNumber($identity)
    {
        if (!$identity instanceof IdentityInterface) {
            throw new InvalidParamException('The identity object must implement IdentityInterface.');
        }
        $key = $this->cacheName.$identity->getId();

        $num = $this->_cache->get($key);
        $num = $num ? intval($num) : 1;

        if($num > $this->maxNumber){
            return false;
        }else{
            $num++;
            $this->_cache->set($key,$num,$this->cacheTime);
        }
        return true;

    }

}