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
//将经纬度转换成城市名和街道地址，参见百度地图接口文档：http://developer.baidu.com/map/index.php?title=webapi/guide/webservice-geocoding
var cityname = function (latitude, longitude, callback) {
	$.ajax({
		url: 'http://api.map.baidu.com/geocoder/v2/?ak=btsVVWf0TM1zUBEbzFz6QqWF&callback=renderReverse&location=' + latitude + ',' + longitude + '&output=json&pois=1',
		type: "get",
		dataType: "jsonp",
		jsonp: "callback",
		success: function (data) {
			console.log(data);
			var province = data.result.addressComponent.province;
			var cityname = (data.result.addressComponent.city);
			var district = data.result.addressComponent.district;
			var street = data.result.addressComponent.street;
			var street_number = data.result.addressComponent.street_number;
			var formatted_address = data.result.formatted_address;
			//localStorage.setItem("province", province);
			//localStorage.setItem("cityname", cityname);
			//localStorage.setItem("district", district);
			//localStorage.setItem("street", street);
			//localStorage.setItem("street_number", street_number);
			//localStorage.setItem("formatted_address", formatted_address);
			//domTempe(cityname,latitude,longitude);
            $('#locationAddress').val(formatted_address);
            if(callback === true){
                checkin(formatted_address);return false;
            }
			var data = {
				latitude: latitude,
				longitude: longitude,
				cityname: cityname
			};
			if (typeof callback == "function") {
				callback(data);
			}
		}
	});
}
    wx.ready(function () {
        <?php if ($this->location): ?>
        wx.getLocation({
            type: 'wgs84',
            success: function (res) {
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                var speed = res.speed; // 速度，以米/每秒计
                var accuracy = res.accuracy; // 位置精度
                $.ajax({
                    type:'GET',
                    url:'<?php echo Yii::app()->createUrl('home/convertgps');?>',
                    data:{lat:latitude,lng:longitude},
                    dataType:'json',
                    success:function(data){
                        cityname(data[0], data[1]);
                    }
                });
                
                //alert(res);
                //alert(JSON.stringify(res));
                
            },
            cancel: function (res) {
                $('#locationAddress').val('拒绝');
                //alert('用户拒绝授权获取地理位置');
            }
        });
        <?php endif;?>
        <?php if ($this->getLocation): ?>
        $('#getLocation').on('click',function(){
            loadindex = layer.open({type:2,shadeClose: false});
            wx.getLocation({
                type: 'wgs84',
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                    $.ajax({
                        type:'GET',
                        url:'<?php echo Yii::app()->createUrl('home/convertgps');?>',
                        data:{lat:latitude,lng:longitude},
                        dataType:'json',
                        success:function(data){
                            cityname(data[0], data[1], true);
                        }
                    });
                    
                    //alert(res);
                    //alert(JSON.stringify(res));
                    
                },
                cancel: function (res) {
                    $('#locationAddress').val('拒绝授权获取地理位置');
                    checkin('拒绝授权获取地理位置');return false;
                    //alert('用户拒绝授权获取地理位置');
                }
            });
        });
        <?php endif;?>
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
                var _postType = parseInt($('#uploadImage').attr('data-type'));
                images.serverId = [];
                function upload() {
                    wx.uploadImage({
                        localId: images.localId[i],
                        success: function (res) {
                            // 直接保存到本地服务器
                            //var uploadUrl = 'url';
                            //$.getJSON(uploadUrl, {media_id: res.serverId}, function (myret) {
                            //alert(myret.error);
                            //$('#uploadImgUrl').val(myret.url);
                            //});
                            
                            if (_postType == 0) {
                                $('#uploadImgUrl').val(res.serverId);
                            }else if(_postType == 1){ // 多文件上传
                                $('#uppics').append('<input class="uppics'+i+'" type="hidden" name="uppics[]" value="'+res.serverId+'">');
                            }
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
                        if (_postType == 0) {
                            if(length != 1){
                                alert('只能上传一张照片！');
                                return false;
                            }
                            $('#uploadImageVal').attr('src', res.localIds[0]);
                            $('#uploadImageVal').parent().parent().show();
                            upload();
                        }else if(_postType == 1){ // 多文件上传
                            $.each(res.localIds,function(i,v){
                                $('#uppics').append('<img class="uppics'+i+'" src="'+v+'" style="width:30%;margin-left:2%;">');
                            });
                            upload();
                        }
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

