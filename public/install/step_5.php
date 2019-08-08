<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $html_title;?></title>
<link href="css/install.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<link href="css/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
</head>
<body>
<?php echo $html_header;?>
<div class="main">
  <div class="final-succeed"> <span class="ico"></span>
    <h2>程序已成功安装</h2>
    <h5>选择您要进入的页面</h5>
  </div>
  <div class="final-site-nav">
    <div class="arrow"></div>
    <ul>
      <li class="shop">
        <div class="ico"></div>
        <h5><a href="/" target="_blank">首页</a></h5>
        <h6>系统首页</h6>
      </li>
      
      <li class="admin">
        <div class="ico"></div>
        <h5><a href="http://gadmin.cojz8.com" target="_blank">官方社区</a></h5>
        <h6>系统后台</h6>
      </li>
    </ul>
  </div>
  <div class="final-intro">
    <p>用户名：admin 密码：123456</p>
  </div>
  <div class="final-intro">
    <p>欢迎使用Gadmin~  如遇问题请到 <a href="http://gadmin.cojz8.com" target="_blank">Gadmin.cojz8.com</a> 官方社区反馈~</p>
  </div>
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
