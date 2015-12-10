<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * <?php $this->widget('application.extensions.wxjs.WxjsWidget',array('appid'=>'','appsecret'=>'','options'=>array('title'=>'','desc'=>'','link'=>'','imgUrl'=>'','uploadImgRoute'=>'')));?>
 */
Yii::import('application.extensions.wxjs.jssdk');
class WxjsWidget extends CWidget{
    public $appid;
    public $appsecret;
    public $options = false;//分享参数
    public $preView = false;//展示图片参数
    public $uploadImg = false;//上传图片
    public $location = false;//直接获取地理位置
    public $getLocation = false;//点击获取地理位置
    public $wxJsSign;
    public function run(){
        $jssdk = new jssdk($this->appid, $this->appsecret);
        $this->wxJsSign = $jssdk->GetSignPackage();
        $this->render('wxjs');
    }
}

