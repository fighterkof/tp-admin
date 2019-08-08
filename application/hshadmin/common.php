<?php

/**斑奇**/

use PHPMailer\PHPMailer\PHPMailer;
use think\Db;

/**
 * @description: 封装的图文编辑器
 * @param {type}
 * @return:
 */
function tpl_ueditor($name = "", $content = "", $height = "500", $width = "100%")
{
    $str = "";

    $str .= '<textarea name="' . $name . '" id="' . $name . '">' . $content . '</textarea>';
    $str .= '<script type="text/javascript">
var url = "' . url('Ueditor/index') . '";
var ue = UE.getEditor(' . "'{$name}'" . ', {
serverUrl: url,
initialFrameWidth: ' . "'{$width}'" . ',
initialFrameHeight: ' . "'{$height}'" . ',
});
</script>';

    return $str;
}

/**
 * @description: 上传附件
 * @param {type}
 * @return:
 */
function UploadFile($name, $value = "")
{
    $str = "";
    $str .= '<div class="layui-input-inline">
    <input class="layui-input" type="text" name="' . $name . '"
        id="' . $name . '" value="' . $value . '" readonly></div>
        <div class="layui-input-block"><a href="javascript:void(0);"
        class="layui-btn" onclick="upFiles(' . "'{$name}'" . ')"><span class="iconfont icon-yunduanshangchuan"></span> 浏览文件</a><a href="javascript:void(0);"
        class="layui-btn" onclick="$(this).parent().parent().find(' . "'input'" . ').val(' . "''" . ')"> 清空</a>
</div>';
    return $str;
}

/**
 * @description: 上传图片
 * @param {type}
 * @return:
 */
function UploadImage($name, $value = "")
{
    $str = "";
    $str .= '<div class="layui-input-inline">
    <input class="layui-input" type="text" name="' . $name . '" id="' . $name . '"
        value="' . $value . '"  readonly></div> <div class="layui-input-block"><a href="javascript:void(0);"
        class="layui-btn" onclick="upImage(' . "'{$name}'" . ')">
        <span class="iconfont icon-yunduanshangchuan"></span> 浏览文件</a><a href="javascript:void(0);"
        class="layui-btn" onclick="$(this).parent().parent().find(' . "'input'" . ').val(' . "''" . ')">清空</a>
</div>';
    return $str;
}

// 数组保存到文件
function arr2file($filename, $arr = '')
{
    if (is_array($arr)) {
        $con = var_export($arr, true);
    } else {
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>";
    write_file($filename, $con);
}
//文件写入
function write_file($l1, $l2 = '')
{
    $dir = dirname($l1);
    if (!is_dir($dir)) {
        mkdirss($dir);
    }

    return @file_put_contents($l1, $l2);
}
//对象转化数组
function obj2arr($obj)
{
    return json_decode(json_encode($obj), true);
}
/**
 * 系统邮件发送函数.
 *
 * @param string $tomail     接收邮件者邮箱
 * @param string $name       接收邮件者名称
 * @param string $subject    邮件主题
 * @param string $body       邮件内容
 * @param string $attachment 附件列表
 *
 * @return bool
 *
 * @author static7 <static7@qq.com>
 */
function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null)
{
    $mail = new PHPMailer(); //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    $mail->SMTPDebug = 0; // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl'; // 使用安全协议
    $mail->Host = config('msg.smtp'); // SMTP 服务器
    $mail->Port = config('msg.eport'); // SMTP服务器的端口号
    $mail->Username = config('msg.euser'); // SMTP服务器用户名
    $mail->Password = config('msg.epwd'); // SMTP服务器密码
    $mail->SetFrom(config('msg.euser'), config('sys.name'));
    $replyEmail = ''; //留空则为发件人EMAIL
    $replyName = ''; //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    return $mail->Send() ? true : $mail->ErrorInfo;
}
/**
 * ajax数据返回，规范格式.
 */
function msg_return($msg = '操作成功！', $code = 0, $data = [], $redirect = 'parent', $alert = '', $close = false, $url = '')
{
    $ret = ['code' => $code, 'msg' => $msg, 'data' => $data];
    $extend['opt'] = [
        'alert' => $alert,
        'close' => $close,
        'redirect' => $redirect,
        'url' => $url,
    ];
    $ret = array_merge($ret, $extend);

    return Response::create($ret, 'json');
}
/**
 * 多维数组合并（支持多数组）.
 *
 * @return array
 */
function array_merge_multi()
{
    $args = func_get_args();
    $array = [];
    foreach ($args as $arg) {
        if (is_array($arg)) {
            foreach ($arg as $k => $v) {
                if (is_array($v)) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : [];
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }

    return $array;
}
/**
 * get_rolename 获取角色名.
 */
function get_rolename($roleid)
{
    return Db('role')->where('id', $roleid)->value('name');
}
/**
 * get_username 获取角色名.
 */
function get_username($uid)
{
    return Db('user')->where('id', $uid)->value('username');
}
/**
 * md5 +盐.
 */
function passCrypt($psw)
{
    $psw = md5($psw);
    $salt = substr($psw, 5, 9);

    $new_psw = md5(sha1($psw . $salt));

    return array('pwd' => $new_psw, 'salt' => $salt);
}
/**
 * 检查后台登录密码
 */
function checkLoginPassword($input_pwd, $db_pwd, $salt)
{
    $input_pwd = md5($input_pwd);
    $new_psw = md5(sha1($input_pwd . $salt));

    if ($new_psw == $db_pwd) {
        return true;
    }
}
