<?php

use think\facade\Request;

/**
 * @description: 微信退款函数
 * @param {type} $total原订单总价，$refundTotal需要退款的金额，$out_trade_no订单号，$transaction_id 微信支付单号
 * @return:
 */

function WeChatRefund($total, $refundTotal, $out_trade_no, $transaction_id)
{

    $config = config('pay.');
    $pay = new \Pay\Pay($config);

    $options = [
        'out_trade_no' => $out_trade_no, // 原商户订单号
        'out_refund_no' => $transaction_id, // 退款订单号
        'total_fee' => $total * 100, // 原订单交易总金额
        'refund_fee' => $refundTotal * 100, // 申请退款金额
    ];
    try {
        $result = $pay->driver('wechat')->gateway('transfer')->refund($options);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    dump($result);

}

/**
 * @description: 生成唯一订单号
 * @param {type}
 * @return:
 */
function get_order_no()
{
    return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
/**
 * 计算几分钟前、几小时前、几天前、几月前、几年前。
 * $agoTime string Unix时间
 * @author tangxinzhuan
 * @version 2016-10-28
 */
function time_ago($agoTime)
{
    $agoTime = (int) $agoTime;

    // 计算出当前日期时间到之前的日期时间的毫秒数，以便进行下一步的计算
    $time = time() - $agoTime;

    if ($time >= 31104000) { // N年前
        $num = (int) ($time / 31104000);
        return $num . '年前';
    }
    if ($time >= 2592000) { // N月前
        $num = (int) ($time / 2592000);
        return $num . '月前';
    }
    if ($time >= 86400) { // N天前
        $num = (int) ($time / 86400);
        return $num . '天前';
    }
    if ($time >= 3600) { // N小时前
        $num = (int) ($time / 3600);
        return $num . '小时前';
    }
    if ($time > 60) { // N分钟前
        $num = (int) ($time / 60);
        return $num . '分钟前';
    }
    return '1分钟前';
}

/**
 * @description: 把图片从临时表删除，计划任务定时清空临时表
 * @param {type}
 * @return:
 */
function cleanImageTemp($image)
{
    $request = Request::instance();

    if (config('app.app_test')) {
        $path = str_replace(config('app.app_test_url'), App::getRootPath() . 'public', $image);
    } else {
        $path = str_replace($request->domain(), App::getRootPath() . 'public', $image);
    }
    db('image_temp')->where('image_url', $image)->delete();
    //@unlink($path);

}

/**
 * @description: 把图片加进临时表，计划任务定时清空临时表
 * @param {string}
 * @return: string
 */
function createImageTemp($image)
{
    db('image_temp')->data(['image_url' => $image])->insert();
}

/** 显示数字，超过10000的显示1.x万
 * @description:
 * @param {string}
 * @return: string
 */
function showNumberV2($number)
{
    if (intval($number) > 9999) {
        $number = $number / 10000;

        return substr(sprintf("%.2f", $number), 0, -1) . "万";

    }
    return "$number";
}

/**
 * @description: 显示数字，超过999的显示999+
 * @param {type}
 * @return:
 */
function showNumber($number)
{

    return intval($number) > 999 ? '999+' : "$number";
}
/**
 * @description: 十六进制 转 RGB
 * @param {type}
 * @return:
 */
function hex2rgb($hexColor, $alpha = "")
{
    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2)),
        );
    } else {
        $color = $hexColor;
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b),
        );
    }
    if ($alpha) {
        $rgb['a'] = $alpha;
    }

    return $rgb;
}

/**
 * @description: RGB转 十六进制
 * @param {type}
 * @return:
 */
function RGBToHex($rgb)
{
    $regexp = "/^rgb\(([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})\)/";
    $re = preg_match($regexp, $rgb, $match);
    $re = array_shift($match);
    $hexColor = "#";
    $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
    for ($i = 0; $i < 3; $i++) {
        $r = null;
        $c = $match[$i];
        $hexAr = array();
        while ($c > 16) {
            $r = $c % 16;
            $c = ($c / 16) >> 0;
            array_push($hexAr, $hex[$r]);
        }
        array_push($hexAr, $hex[$c]);
        $ret = array_reverse($hexAr);
        $item = implode('', $ret);
        $item = str_pad($item, 2, '0', STR_PAD_LEFT);
        $hexColor .= $item;
    }
    return $hexColor;
}

/**
 * @description: 根据图片地址来获取图像的宽高
 * @param {type}
 * @return:
 */
function getURLwidthHeight($string, $type = 'w')
{
    if ($string) {
        $str = explode('_', $string);
        if ($type == 'w') {
            return intval($str[1]);
        } elseif ($type == 'h') {
            return intval($str[2]);
        }
    }
}

/**
 * @description: 设置图片路径
 *
 * @param {type}
 * @return:
 */
function tomedia($image)
{
    if ($image) {
        $request = Request::instance();
        if (stripos($image, 'http://') === 0 || stripos($image, 'https://') === 0) {
            return $image;
        } else {
            if (config('app.app_test')) {
                return config('app.app_test_url') . '/' . $image;
            } else {
                return $request->domain() . '/' . $image;
            }
        }
    } else {
        return "";
    }
}

/**
 * @description: 富文本图片路径
 *
 * @param {type}
 * @return:
 */
function imageUrl($content)
{
    if (preg_match('/(http:\/\/)|(https:\/\/)/i', $content)) {
        $url = "";
    } else {

        $url = config('app.app_test_url') . '/';
    }
    $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
    $list = preg_replace($pregRule, '<img src="' . $url . '${1}" style="max-width:100%">', $content);
    return $list;
}

function returnMsg($code, $msg = '', $data = [], $page = '')
{
    $return_data['code'] = $code;
    $return_data['msg'] = $msg;
    if (empty($data)) {
        $data = [];
    }
    if ($page !== '') {
        $return_data['page'] = $page;
    }
    $return_data['data'] = $data;

    echo json_encode($return_data);
    die;
}
