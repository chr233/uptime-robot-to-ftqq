<?php
/*
 *一个很简单的微信推送的api,用来对接uptime robot的webhook功能
 *author: Chr_
 *email: chr@chrxw.com
 *website: https://blog.chrxw.com
 *
 *token:
 *用于验证身份,不需要的话留空即可
 *设置以后需要以http:/xx.com?token=xxx的格式访问
 *token错误会直接返回404,防止被恶意使用
 *
 *skey:
 *用于方糖气球微信推送
 *访问http://sc.ftqq.com/?c=code登录获取
*/
$token = '';
$skey = '';
if ($_GET && $_GET['token'] == $token) {
    if ($_GET['alertType'] == 1) { //down
        $text = '服务器下锅啦';
        $desp = '### 名称: ' . $_GET['monitorFriendlyName'] . "\n";
        $desp.= '#### 网址: [' . $_GET['monitorURL'] . '](' . $_GET['monitorURL'] . ')' . "\n";
        $desp.= '#### 状态: 故障' . "\n";
        $desp.= '#### 时间: ' . date('Y-m-d H:i:s', (int)$_GET['alertDateTime']) . "\n";
        $desp.= '#### 详情: ' . $_GET['alertDetails'];
    } elseif ($_GET['alertType'] == '2') { //up
        $text = '服务器捞起来啦';
        $seconds = (int)$_GET['alertDuration'];
        if ($seconds > 3600) {
            $hours = intval($seconds / 3600);
            $minutes = $seconds % 3600;
            $time = $hours . "小时" . gmstrftime('%M分钟%S秒', $minutes);
        } else {
            $time = gmstrftime('%H小时%M分钟%S秒', $seconds);
        }
        $desp = '## 名称: [' . $_GET['monitorFriendlyName'] . ']' . "\n";
        $desp.= '#### 网址: [' . $_GET['monitorURL'] . '](' . $_GET['monitorURL'] . ')' . "\n";
        $desp.= '#### 状态: 运行中' . "\n";
        $desp.= '#### 时间: ' . date('Y-m-d H:i:s', (int)$_GET['alertDateTime']) . "\n";
        $desp.= '#### 持续: ' . $time . "\n";
        $desp.= '#### 详情: ' . $_GET['alertDetails'];
    } else {
        abort(500);
    }
    $opts = array('http' => array('method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => http_build_query(array('text' => $text, 'desp' => $desp))));
    $context = stream_context_create($opts);
    $result = file_get_contents('https://sc.ftqq.com/' . $skey . '.send?', false, $context);
    echo $result;
} else {
    abort(404);
}
?>
