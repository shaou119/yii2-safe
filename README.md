# yii2-safe
Yii2 login  safe extension

Yii2 登录安全机制
登录频率限制
使用方法

    配置文件  main.php
	
      'components' => [
				  //与 cacheName 对应，缓存存储方式可自己定义
				  'cache' => [
					  'class' => 'yii\caching\FileCache',
				  ],
				  'loginValidation'=>[
					  'class'=>'yii\safe\Extension',//登录限制类
					  'cacheName'=>'cache', //缓存名 对应 loginCache组件 可自定义
					  'maxNumber'=>100777, //最大错误次数
					  'cacheTime'=>5,  //缓存时间 单位：分钟
					  'method'=>[
						  'checkNumber', //执行函数
					]
                          
        ],
		
    使用 LoginFrom.php
	 
      public function beforeValidate()
      {
          parent::beforeValidate();

          $user = User::findByUsername($this->username);
          if($user){
              if(Yii::$app->loginValidation->run($user)===false){
                  $time = \Yii::$app->loginValidation->cacheTime / 60;
                  if($time){
                      $message = 'Login try after '.$time.' minutes later';
                  }else{
                      $message = 'Login too much !';
                  }
                  $this->addError('password',$message);
                  return false;
              }
              return true;
          }else{
              $this->addError('password','Please check your username.');
              return false;

          }


      }





