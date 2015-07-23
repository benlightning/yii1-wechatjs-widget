# yii1-wechatjs-widget
a wechat js widget for yii1.x

#保存微信图片到本地服务器
```php
public function saveWeixinFile($media_id) {
	if ($media_id) {
	    $filecontent = $this->wechatModel->getMedia($media_id);
	    if ($filecontent) {
		$uploadPath = '/upload/weixin/' . date('Ym') . '/';
		if (!is_dir(__PATH__ . $uploadPath)) {
		    mkdir('.' . $uploadPath, 0777, true);
		}
		$filename = uniqid(date('d-His')) . mt_rand(1000, 9999) . '.jpg';
		$local_file = fopen(__PATH__ . $uploadPath . $filename, 'w');
		if (false !== $local_file) {
		    if (false !== fwrite($local_file, $filecontent)) {
		        fclose($local_file);
		    }
		}
		return $uploadPath . $filename;
	    }
	}
	return false;
}
```

#获取微信网页授权用户信息
```php
    public $wechatModel; //微信公众号模型
    public function init() {
        parent::init();
        $options = array(
            'token' => TOKEN,
            'appid' => APPID, //填写高级调用功能的app id
            'appsecret' => APPSECRET, //填写高级调用功能的密钥
        );
        $this->wechatModel = new Wechat($options);
        $url_weixin_base = $this->wechatModel->getOauthRedirect(Yii::app()->request->hostInfo . Yii::app()->request->url, '', 'snsapi_base');
        $url_weixin_userinfo = $this->wechatModel->getOauthRedirect(Yii::app()->request->hostInfo . Yii::app()->request->url);

        $info = Yii::app()->session['userInfo'];
        $model = new Member();
        $wechatUserInfo = null;
        if (!$info) {
            $token = $this->wechatModel->getOauthAccessToken();
            if (empty($token['openid'])) {
                $this->redirect($url_weixin_base);
                exit;
            }
            $userInfo = $model->findByAttributes(array('OPENID' => $token['openid']));
            if ($userInfo) {
                Yii::app()->session['userInfo'] = $userInfo->attributes;
            } else {
                $wechatUserInfo = $this->wechatModel->getOauthUserinfo($token['access_token'], $token['openid']);
                if (!$wechatUserInfo) {
                    //$user_agent = $_SERVER['HTTP_USER_AGENT'];
                    //if (strpos($user_agent,'MicroMessenger') !== false){
                    $this->redirect($url_weixin_userinfo);exit; //认证后开启
                    //}
                }
            }
        }
        if ($wechatUserInfo) {
            $model->NICKNAME = $wechatUserInfo['nickname'];
            $model->OPENID = $wechatUserInfo['openid'];
            $model->GENDER = $wechatUserInfo['sex'];
            $model->HEADIMG = $wechatUserInfo['headimgurl'];
            //$model->country = $wechatUserInfo['country'];
            //$model->province = $wechatUserInfo['province'];
            //$model->city = $wechatUserInfo['city'];
            if ($model->save()) {
                Yii::app()->session['userInfo'] = $model->attributes;
            }
        }
    }
```
