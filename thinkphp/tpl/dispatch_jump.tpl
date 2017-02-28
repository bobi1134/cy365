{__NOLAYOUT__}<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>创业365</title>

    <link href="/static/index/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/sweetalert/1.1.3/sweetalert.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/static/index/css/font-awesome.min.css">
    <link rel="stylesheet" href="/static/index/css/swiper.min.css">
    <link rel="stylesheet" href="/static/index/css/square/_all.css">


    <!--[if IE 7]>
    <link rel="stylesheet" href="/static/index/css/font-awesome-ie7.min.css">
    <![endif]-->
    {block name="ext_css"}{/block}  <!--扩展css样式-->
    <link rel="stylesheet" href="/static/index/css/index.css">
    <link rel="stylesheet" href="/static/index/css/lorin.css">
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/vue/1.0.24/vue.min.js"></script>
</head>
<body style="background-color: #eaeaea;">
<div class="nav-square">
    <div class="container">
        <div class="row">
            <div class="navbar no-margin no-border" role="navigation">
                <div class="navbar-header col-lg-2 col-md-2 col-sm-2">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="sr-only"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="{:url('index/index/index')}" class="navbar-brand">
                        <img height="20" src="/static/index/images/logo.svg" onerror="this.src='/static/index/images/logo.png'" alt="">
                    </a>
                </div>
                <div class="collapse navbar-collapse navbar-responsive-collapse col-lg-10 col-md-10 col-sm-10">
                    <ul class="nav">
                        <li class="{if(\think\Request::instance()->controller())=="index"}active{/if} col-lg-1 col-md-1 col-sm-1">
                            <a href="{:url('index/index/index')}" class="text-center">首  页<i></i></a>
                        </li>
                        <div class="col-lg-8 col-md-9 col-sm-9 no-padding">
                            <li class="{if(\think\Request::instance()->controller())=="news"}active{/if} col-lg-2 col-md-2 col-sm-2">
                                <a href="{:url('index/news/index')}" class="text-center">创业资讯<i></i></a>
                            </li>
                            <li class="{if(\think\Request::instance()->controller())=="education"}active{/if} col-lg-2 col-md-2 col-sm-2">
                                <a href="{:url('index/education/index')}" class="text-center">创业教育<i></i></a>
                            </li>
                            <li class="{if(\think\Request::instance()->controller())=="service"}active{/if} col-lg-2 col-md-2 col-sm-2">
                                <a href="{:url('index/service/index')}" class="text-center">创业服务<i></i></a>
                            </li>
                            <li class="{if(\think\Request::instance()->controller())=="hatch"}active{/if} col-lg-2 col-md-2 col-sm-2">
                                <a href="{:url('index/hatch/index')}" class="text-center">创业孵化<i></i></a>
                            </li>
                            <li class="{if(\think\Request::instance()->controller())=="genius"}active{/if} col-lg-2 col-md-2 col-sm-2">
                                <a href="{:url('index/genius/index')}" class="text-center">项目展示<i></i></a>
                            </li>
                            <li class="{if(\think\Request::instance()->controller())=="cooperate"}active{/if} col-lg-2 col-md-2 col-sm-2">
                                <a href="{:url('index/cooperate/index')}" class="text-center">风投对接<i></i></a>
                            </li>
                        </div>
                        <div class="col-lg-3 col-md-2 col-sm-2 sm-no-padding">
                            {if(\think\Session::get('user_id')=="")}
                                <a href="javascript:void(0);" class="text-center col-lg-2 col-lg-offset-7 col-md-4 col-md-offset-3 col-sm-5 col-sm-offset-0 col-xs-2 col-xs-offset-3" onclick="NavLogin()">登录</a>
                                <span class="text-center col-lg-1 col-md-1 col-sm-2 col-xs-2">|</span>
                                <a href="javascript:void(0);" class="text-center col-lg-2 col-md-4 col-sm-5 col-xs-2" onclick="NavSignUp()">注册</a>
                            {/if}
                            {if(\think\Session::get('user_id')!="")}
                                <a href="{:url('user/index/index')}" class="text-center col-lg-3 col-lg-offset-6 col-md-4 col-md-offset-3 col-sm-5 col-sm-offset-0 col-xs-2 col-xs-offset-3">
                                    {:\\think\\Cookie::get('username')}</a>
                                <span class="text-center col-lg-1 col-md-1 col-sm-2 col-xs-2">|</span>
                                <a href="#" @click.stop.prevent="logout" class="text-center col-lg-2 col-md-4 col-sm-5 col-xs-2">退出</a>
                            {/if}
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
{if(\think\Session::get('user_id')=="")}
    <!-- 弹出登陆模态框 -->
    <script>
        function NavLogin() {
            $("#nav_login").modal("toggle");
        }
        function NavSignUp() {
            $("#nav_signUp").modal("toggle");
        }
    </script>
    {js href="/static/index/js/icheck.js" /}
    <div class="modal" id='nav_login'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <a href="javascript:void(0);" class="block text-center padding-10">
                        <img height="20" src="/static/index/images/logo.svg" onerror="this.src='/static/index/images/logo.png'" alt="创业365">
                    </a>
                    <form  method="post">
                        <input type="text" name="username" v-model="userName" class="user-one" placeholder="账户" required>
                        <input type="password" name="passwd" v-model="password" class="user-one" placeholder="密码" required>
                        <div class="checkbox" id="check-value">
                            <input type="checkbox" id="square-checkbox-1" name="remember" >
                            <label for="square-checkbox-1">记住登录状态&nbsp;&nbsp;&nbsp;{{ remember }}<span><a href="javascript:void(0);" @click.stop.prevent="forgetPsw">忘记密码？</a></span></label>
                        </div>
                        <input type="submit" name="submit" @click.stop.prevent="login()" class="user-submit btn-blue" value="登 录">
                        <div class="goregist">
                            <a href="javascript:void(0);" @click="goToSignUp()" class="block pull-right">无账户名？单击注册</a>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function(){
                            $('.checkbox input').iCheck({
                                checkboxClass: 'icheckbox_square-blue',
                                increaseArea: '10%'
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id='nav_forget'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <a href="javascript:void(0);" class="block text-center padding-10">
                        <img height="20" src="/static/index/images/logo.svg" onerror="this.src='/static/index/images/logo.png'" alt="创业365">
                    </a>
                    <form  method="post">
                        <input type="text" name="username" v-model="userName" class="user-one" autocomplete="off" placeholder="请输入您的账户" required>
                        <input type="email" name="email" v-model="email" class="user-one" autocomplete="off" placeholder="请输入您的邮箱">
                        <div class="row">
                            <div class="col-md-7">
                                <input type="text" name="verify" v-model="verify" autocomplete="off" class="user-one col-md-9" placeholder="请输入验证码">

                            </div>
                            <div class="col-md-5">
                                <button type="button" class="user-submit btn-blue" @click="getVerify">获取验证码</button>

                            </div>
                        </div>

                        <input type="password" name="passwd" v-model="password" autocomplete="off" class="user-one" placeholder="请输入新密码" required>

                        <input type="submit" name="submit" @click.stop.prevent="updatePasswd" class="user-submit btn-blue" value="重置密码">
                    </form>
                    <script>
                        $(document).ready(function(){
                            $('.checkbox input').iCheck({
                                checkboxClass: 'icheckbox_square-blue',
                                increaseArea: '10%'
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id='nav_signUp'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <a href="javascript:void(0);" class="block text-center padding-10">
                        <img height="20" src="/static/index/images/logo.svg" onerror="this.src='/static/index/images/logo.png'" alt="创业365">
                    </a>
                    <form  method="post">
                        <input type="text" name="username" v-model="userName" v-on:blur="availability()" id="inputHelpBlock" class="user-one" placeholder="账户" required aria-describedby="helpBlock">
                        <span id="helpBlock" class="help-block" style="display: none"></span>
                        <input type="password" name="passwd" v-model="password" class="user-one" placeholder="登录密码" required>
                        <input type="email" name="email" v-model="email" class="user-one" placeholder="绑定邮箱" required>
                        <input type="text" name="phone" v-model="phone" class="user-one" placeholder="联系电话" required>
                        <input type="submit" name="submit" @click.stop.prevent="signUp()" class="user-submit btn-blue" value="注册">
                        <div class="goregist">
                            <a href="javascript:void(0);" @click="goToLogin()" class="block pull-right">已有账户？单击登录</a>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function(){
                            $('.checkbox input').iCheck({
                                checkboxClass: 'icheckbox_square-blue',
                                increaseArea: '10%'
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
{/if}
<script>
    var vm=new Vue({
        el:"body",
        data:{
            userName:"",
            password:"",
            email:"",
            phone:"",
            verify:""
        },
        methods:{
            login:function(){
                var this_el=event.currentTarget;
                var rem=$("#check-value").children("div").hasClass('checked');
                var data={username:vm.userName,passwd:vm.password,remember:rem==true?'on':'off'};
                $.ajax({
                    type:"post",
                    dataType:"json",
                    data:data,
                    url:"{:url('user/login/login')}",
                    beforeSend: function () {
                        $(this_el).val("登录中");
                    },
                    success:function(res){
                        if(res.code===1){
                            location.reload();
                        }else{
                            swal({
                                title:'',
                                text:res.msg,
                                type:"error",
                                closeOnConfirm:false
                            });
                            $(this_el).val("登 录");
                        }
                    }
                })
            },
            signUp:function(){
                var this_el=event.currentTarget;
                var data={username:vm.userName,passwd:vm.password,email:vm.email,phone:vm.phone};
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    data:data,
                    url:"{:url('user/login/register')}",
                    beforeSend:function(){
                        $(this_el).val("正在注册中");
                    },
                    success:function(res) {
                        if (res.code === 1) {
                            swal({
                                title:'',
                                text:res.msg,
                                type:"success",
                                confirmButtonText: "好的"
                            }, function(){
                                location.reload();
                            })
                        } else {
                            swal({
                                title:'',
                                text:res.msg,
                                type:"error",
                                closeOnConfirm:false
                            });
                            $(this_el).val("注册");
                        }
                    },
                    error:function(){
                        swal("系统错误");
                    }
                });
            },
            logout:function(){
                $.getJSON({
                    url:"{:url('user/login/logout')}",
                    success:function(res){
                        swal(res.msg);
                    }
                })
                location.href="{:url('index/index/index')}";
            },
            availability:function(){
                if(vm.userName=='')
                    return false;
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    data:{username:vm.userName},
                    url:"{:url('user/login/availability')}",
                    beforeSend:function(){
                        $("#helpBlock").text("正在检查您的用户名...").removeClass('text-danger','text-success').addClass('text-muted').css({"display":"block"});
                    },
                    success:function(res){
                        if(res.code==0){
                            $("#helpBlock").text(res.msg).removeClass('text-success').addClass('text-danger').css('display','block');
                        }else{
                            $("#helpBlock").text(res.msg).removeClass('text-danger').addClass('text-success').css('display','block');
                        }
                    }
                })
            },
            goToSignUp:function(){
                $("#nav_login").modal("toggle");
                $("#nav_signUp").modal("toggle");
            },
            goToLogin:function(){
                $("#nav_signUp").modal("toggle");
                $("#nav_login").modal("toggle");
            },
            forgetPsw:function(){
                $("#nav_login").modal("toggle");
                $("#nav_forget").modal("toggle");
            },
            getVerify:function(){
                var this_el=event.currentTarget;
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    data:{username:vm.userName,email:vm.email},
                    url:"{:url('user/login/sendPasswdVerify')}",
                    beforeSend:function(){
                        $(this_el).text("正在发送...");
                    },
                    success:function(res){
                        if(res.code==1){

                            $(this_el).text(res.msg)
                        }else{
                            $(this_el).text("发送失败");
                            swal({
                                title:"",
                                text:res.msg,
                                type:"error"
                            })
                        }
                    },
                    error:function(){
                        swal({
                            title:"",
                            text:"抱歉,服务器出错了",
                            type:"error"
                        })
                    }
                })
            },
            updatePasswd:function(){
                var data={username:vm.userName,new_passwd:vm.password,verify_code:vm.verify};
                var this_el=event.currentTarget;
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    data:data,
                    url:"{:url('user/login/updatePasswd')}",
                    beforeSend:function(){
                        $(this_el).text("正在发送中");
                    },
                    success:function(res){
                        if(res.code==1){
                            swal({
                                title:"",
                                text:res.msg,
                                type:"success"
                            },function(){
                                location.reload()
                            })
                        }else{
                            swal({
                                title:"",
                                text:res.msg,
                                type:"error"
                            })
                        }
                        $(this_el).text("重置密码")
                    },
                    error:function(){
                        swal({
                            title:"",
                            text:"抱歉,系统出错了",
                            type:"error"
                        })
                    }
                })
            }
        }
    })
</script>
<div class="container">
    <div class="row" style="margin: 100px 0;">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-ss-12 col-lg-offset-2 col-md-offset-2" >
            <div class="box-template border-gray" style="min-height: 375px;">
                <?php switch ($code) {?>
                <?php case 1:?>
                <h2 class="text-center"> <img src="/static/index/images/logo.png"> </h2>
                <h2 class="box-title"><?php echo(strip_tags($msg));?></h2>

                <p class="jump" style="margin-top:30px">
                页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
                </p>
                <?php break;?>
                <?php case 0:?>
                <h2 class="text-center"> <img src="/static/index/images/logo.png"> </h2>
                <h2 class="box-title"><?php echo(strip_tags($msg));?></h2>

                <p class="jump" style="margin-top:30px">
                页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
                </p>
                <?php break;?>
                <?php } ?>
            </div>
        </div>
        </div>
    </div>

</div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
<div class="footer-low">
    <div class="container">
        <div class="row">
            <div class="col-ss-12 col-xs-12 col-sm-12 col-md-10 col-lg-8">
                <p>
                    <span><a href="{:url('index/index/index')}">网站首页</a></span>
                    <span><a href="javascript:void(0);">创业培训</a></span>
                    <span><a href="javascript:void(0);">创业测评</a></span>
                    <span><a href="javascript:void(0);">优秀案例</a></span>
                    <span><a href="javascript:void(0);">招聘信息</a></span>
                    <span><a href="javascript:void(0);">联系我们</a></span>
                    <span><a href="javascript:void(0);">关于我们</a></span>
                    <span><a href="javascript:void(0);">友情链接</a></span>
                </p>
                <p>
                    Copyright © {:date("Y")} &nbsp;&nbsp; 四川农业大学创业指导中心 &nbsp;&nbsp;  All Rights Reserved |
                </p>
            </div>
            <div class="col-md-2 col-md-offset-0 col-lg-2 col-lg-offset-2 hidden-ss hidden-xs hidden-sm">
                <div class="footer-low-share" id="footer-low-share"></div>
                <script>
                    function WeiXinShareBtn() {
                        $("#share_wechat").modal("toggle");
                    }
                </script>
                <script>
                    var loca_url = window.location.href;
                    document.getElementById('footer-low-share').innerHTML = "<a href='http://connect.qq.com/widget/shareqq/index.html?url=http%3A%2F%2F"+loca_url+"%2Fcy%2F&desc=&title=&summary=&pics=&flash=&site=&showcount=' title='分享到QQ' class='footer-low-share-qq' target='_blank'></a><a href='http://service.weibo.com/share/share.php?url=%3A%2F%2F"+loca_url+"&appkey=&title=&pic=&ralateUid=&language=' class='footer-low-share-sina' title='分享到新浪微博' target='_blank'></a><a href='javascript:void(0)' onclick='WeiXinShareBtn()' class='footer-low-share-wechat' title='分享到微信' target='_blank'></a>";
                </script>
            </div>
        </div>
    </div>
</div>
<!-- 关注微信公众号，弹出模态框 -->
<div class="modal" id='share_wechat'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">请扫描下方二维码，关注微信公众号，谢谢！</h4>
            </div>
            <div class="modal-body">
                <p class="text-center"><img width="210" height="210" src="/static/index/images/cy365-qr.jpg" alt="川农就业微信公众号"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <a href="/static/index/images/cy365-qr.jpg" class="btn btn-primary">下载</a>
            </div>
        </div>
    </div>
</div>
<script src="/static/index/js/bootstrap.min.js"></script>
<script src="/static/index/js/sweetalert.min.js"></script>
<script src="//cdn.bootcss.com/vue/1.0.24/vue.min.js"></script>
{block name="ext_js"}{/block}  <!--扩展js样式-->
</body>
</html>