# yii1-wechatjs-widget
a wechat js widget for yii1.x

#保存微信图片到本地服务器
<code>
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
</code>
