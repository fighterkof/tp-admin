<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $html_title;?></title>
<link href="css/install.css" rel="stylesheet" type="text/css">
<link href="css/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
</head>
<body>
<?php echo $html_header;?>
<div class="main">
  <div class="text-box" id="text-box">
    <div class="license">
      <h1>系统安装协议</h1>
      <p>感谢您选择本系统。本系统是自主开发汇集TPFD、TPflow、RBAC等等超级便捷的极简万能开发后台。官方网址为 http://gadmin.cojz8.com。</p>
      <p>用户须知：您若安装即为表示您同意本协议。</p>
    
      <p>在理解、同意、并遵守本协议的全部条款后，方可开始使用Gadmin后台框架。</p>
      <p>Gadmin团队拥有本软件的全部知识产权。本软件只供许可协议，并非出售。</p>
      <h3>I. 协议许可的权利</h3>
      <ol>
        <li>您可以在完全遵守本许可协议的基础上，将本软件应用于商业用途，而不必支付软件版权许可费用。</li>
        <li>您可以在遵循MIT开源协议规定的约束和限制范围内修源代码而不需要作者授权。</li>
        <li>您可以在本软件基础上进行修改、再编辑、甚至应用于商业项目，但您必须保留本软件相关标识。</li>
      </ol>
      <p></p>

      <p></p>
      <p align="right">Gadmin研发团队</p>
	  <p align="right">2019年3月1日</p>
    </div>
  </div>
  <div class="btn-box"><a href="index.php?step=1" class="btn btn-primary">同意协议进入安装</a><a href="javascript:window.close()" class="btn">不同意</a></div>
</div>
<?php echo $html_footer;?>
<script type="text/javascript">
$(document).ready(function(){
    //自定义滚定条
    $('#text-box').perfectScrollbar();
});
</script>
</body>
</html>
