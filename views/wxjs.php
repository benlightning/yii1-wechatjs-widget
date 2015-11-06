<script  src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '<?php echo $this->wxJsSign["appId"]; ?>',
        timestamp: <?php echo $this->wxJsSign["timestamp"]; ?>,
        nonceStr: '<?php echo $this->wxJsSign["nonceStr"]; ?>',
        signature: '<?php echo $this->wxJsSign["signature"]; ?>',
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'translateVoice',
            'startRecord',
            'stopRecord',
            'onRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard'
        ]
    });
</script>
<script>
    wx.ready(function () {
<?php if ($this->options): ?>
            var shareData = {
                title: '<?php echo $this->options['title']; ?>',
                desc: '<?php echo $this->options['desc']; ?>',
                link: '<?php echo $this->options['link']; ?>',
                imgUrl: '<?php echo $this->options['imgUrl']; ?>',
                trigger: function (res) {
                    // alert('用户点击发送给朋友');
                },
                success: function (res) {
                    //alert('已分享');
                    //$.getJSON('<?php echo Yii::app()->request->url; ?>', {shareyes: 99}, function () {

                    //});
                },
                cancel: function (res) {

                },
                fail: function (res) {

                }
            };
            wx.onMenuShareAppMessage(shareData);
            wx.onMenuShareTimeline(shareData);
            wx.onMenuShareQQ(shareData);
            wx.onMenuShareWeibo(shareData);
<?php endif; ?>
        // 5 图片接口
        // 5.1 拍照、本地选图
        var images = {
            localId: [],
            serverId: []
        };
<?php if ($this->uploadImg): ?>
            // 5.3 上传图片
            document.querySelector('#uploadImage').onclick = function () {
                var i = 0, length = 0;
                images.serverId = [];
                function upload() {
                    wx.uploadImage({
                        localId: images.localId[i],
                        success: function (res) {
                            // 直接保存到本地服务器
                            //var uploadUrl = '<?php echo Yii::app()->createAbsoluteUrl($this->options['uploadImgRoute']); ?>';
                            //$.getJSON(uploadUrl, {media_id: res.serverId}, function (myret) {
                            //alert(myret.error);
                            //$('#uploadImgUrl').val(myret.url);
                            //});
                            $('#uploadImgUrl').val(res.serverId);
                            i++;
                            // alert('已上传：' + i + '/' + length);
                            images.serverId.push(res.serverId);
                            if (i < length) {
                                upload();
                            }
                        },
                        fail: function (res) {
                            alert(JSON.stringify(res));
                        }
                    });
                }
                wx.chooseImage({
                    success: function (res) {
                        images.localId = res.localIds;
                        length = images.localId.length;
                        $('#uploadImage').attr('src', res.localIds[0]);
                        if (images.localId.length != 1) {
                            alert('请选择一张照片！');
                            return;
                        }
                        upload();
                        //alert('已选择 ' + res.localIds.length + ' 张图片');
                    }
                });
            };
<?php endif; ?>
<?php if ($this->preView): ?>
            $(document).on('click', '<?php echo $this->preView['class']; ?>', function () {
                wx.previewImage({
                    current: $(this).attr('data-value'),
                    urls: [<?php echo $this->preView['imgurls']; ?>]
                });
            });
            // 5.2 图片预览
<?php endif; ?>
        // 6 设备信息接口
        // 6.1 获取当前网络状态
        document.querySelector('#getNetworkType').onclick = function () {
            wx.getNetworkType({
                success: function (res) {
                    alert(res.networkType);
                },
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });
        };

    });
    wx.error(function (res) {
//  alert(res.errMsg);
    });

</script>

