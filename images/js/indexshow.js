//auto scroll
var cfg={
    "scroll":500,//滚动时间
    "stop":3000,//停留时间
    "num":5//图片数
};
function run(){
    if(parseInt($("#pnl_scroll").css("left"))>-(565*(cfg.num-1)))
    {
        run.index++;
        $("#pnl_scroll").animate({left : '-=565px'}, cfg.scroll,function(){
            $("#pnl_btn li.on").removeClass("on");
            $("#pnl_btn li").eq(run.index).addClass("on");
            start_auto();
        });
    }
    else
    {
        run.index=0;
        $("#pnl_scroll").animate({left : '0px'}, cfg.scroll,function(){
            $("#pnl_btn li.on").removeClass("on");
            $("#pnl_btn li").eq(run.index).addClass("on");
            start_auto();
        });
    }
}
run.index=0;
run.time=0;
function go_to(index){
    run.index=index;
    var left=565*index;
    $("#pnl_scroll").animate({left: '-'+left+'px'}, cfg.scroll,function(){
        $("#pnl_btn li.on").removeClass("on");
        $("#pnl_btn li").eq(run.index).addClass("on");
    });
}

function start_auto(){
    stop_auto();
    run.time=setTimeout(run,cfg.stop);
}
function stop_auto(){
    clearTimeout(run.time);
}
$(function(){
    start_auto();
    $("#pnl_btn,#pnl_scroll").hover(function(){
        stop_auto();
    },function(){
        start_auto();
    });
    $("#pnl_btn li").each(function(i,j){
        $(this).click(function(){
            go_to(i);
        });
    });
    setInterval(function(){
        $("li",$("#pnl_ey")).first().appendTo($("#pnl_ey"));
    },4000);
    $("#pnl_speak_b").mouseenter(function(){
        $("#pnl_speak").height(130);
    }).mouseleave(function(){
            $("#pnl_speak").height(55);
        });
    $("#pnl_together_b").mouseenter(function(){
        $("#pnl_together").show();
    }).mouseleave(function(){
            $("#pnl_together").hide();
        });
    $('#slide_box').mfwSlide();
});
//寻找旅行家 自动翻转
$.fn.mfwSlide = function(options) {
    var settings = $.extend( {
        'width' : 260,
        'height': 240,
        'speed' : 300,
        'thumb_box' : '.slide_tab',
        'prev_btn' : '',
        'next_btn' : '',
        'thumb_focus_class' : 'on',
        'auto_play' : true,
        'interval' : 10
    }, options);
    return this.each(function(){
        var i = 0; //当前图片索引
        $(this).width(settings.width).height(settings.height);
        var li = $(this).find('ul>li').css('position', 'absolute');
        var n = li.length-1; //图片总数
        var speed = settings.speed;
        //li.find('img').width(settings.width).height(settings.height);
        if(n>0){
            li.not(":first").css({left:settings.width + "px"});
            li.eq(n).css({left:'-'+settings.width + "px"});

            var thumb_box = $(this).find(settings['thumb_box']).css('overflow', 'hidden');
            var thumb = thumb_box.find('li');
            thumb.eq(0).addClass(settings['thumb_focus_class']);

            var fun_next_img = function (){
                if (!li.is(":animated")) {
                    li.eq(i).animate({left:'-' + settings.width + "px"},{duration :speed});
                    thumb.filter('.'+settings['thumb_focus_class']).removeClass(settings['thumb_focus_class']);
                    if(n==1){
                        li.eq(!i).css({left:settings.width + "px"});
                        li.eq(!i).animate({left:'0'},{duration :speed});
                        i=!i;
                        thumb.eq(i).addClass(settings['thumb_focus_class']);
                    } else {
                        if (i>=n){
                            li.eq(0).animate({left:"0"},{duration :speed, complete:function(){
                                li.eq(n-1).css({left:settings.width + "px"});
                                i = 0;
                                thumb.eq(i).addClass(settings['thumb_focus_class']);
                            }});
                        }else{
                            li.eq(i+1).animate({left:"0"},{duration :speed, complete:function(){
                                if(i==0){
                                    li.eq(n).css({left:settings.width + "px"});
                                } else {
                                    li.eq(i-1).css({left:settings.width + "px"});
                                }
                                i++;
                                thumb.eq(i).addClass(settings['thumb_focus_class']);
                            }});
                        };
                    }
                };
            };

            var fun_prev_img = function (){
                if (!li.is(":animated")) {

                    li.eq(i).animate({left:settings.width + "px"},{duration :speed});
                    thumb.filter('.'+settings['thumb_focus_class']).removeClass(settings['thumb_focus_class']);
                    if(n==1){
                        li.eq(!i).css({left:"-" + settings.width + "px"});
                        li.eq(!i).animate({left:'0'},{duration :speed});
                        i=!i;
                        thumb.eq(i).addClass(settings['thumb_focus_class']);
                    } else {
                        if (i<=0){
                            li.eq(n).animate({left:"0"},{duration :speed, complete:function(){
                                li.eq(n-1).css({left:'-'+settings.width + "px"});
                                i = n;
                                thumb.eq(i).addClass(settings['thumb_focus_class']);
                            }});
                        }else{
                            li.eq(i-1).animate({left:"0"},{duration :speed, complete:function(){
                                if(i==1){
                                    li.eq(n).css({left:'-'+settings.width + "px"});
                                } else {
                                    li.eq(i-2).css({left:'-'+settings.width + "px"});
                                }
                                i--;
                                thumb.eq(i).addClass(settings['thumb_focus_class']);
                            }});
                        }
                    }
                };
            };

            fun_jump_img = function($this){
                var id = $this.data('id');
                thumb.filter('.'+settings['thumb_focus_class']).removeClass(settings['thumb_focus_class']);
                $this.addClass(settings['thumb_focus_class']);

                if(i!=id){
                    li.eq(i).animate({left:'-' + settings.width + "px"},{duration :speed});
                    li.eq(id).css({left:settings.width + "px"});
                    li.eq(id).animate({left:'0'},{duration :speed, complete:function(){
                        if(id==0){
                            li.eq(n).css({left:'-'+settings.width + "px"});
                        } else {
                            li.eq(id-1).css({left:'-'+settings.width + "px"});
                        }
                        if(id==n){
                            li.eq(0).css({left:settings.width + "px"});
                        } else {
                            li.eq(id+1).css({left:settings.width + "px"});
                        }
                        i = id;
                    }});
                }
            };

            if(settings['auto_play']){
                time = setInterval(fun_next_img, settings['interval']*1000);
            }
            if(settings.next_btn!=''){
                $(settings['next_btn']).click(function(){
                    clearInterval(time);
                    fun_next_img();
                });
            }
            if(settings.prev_btn!=''){
                $(settings['prev_btn']).click(function(){
                    clearInterval(time);
                    fun_prev_img();
                });
            }
            thumb.click(function(){
                clearInterval(time);
                var $this = $(this);
                fun_jump_img($this);
            });

        }
    });
}

//顶文章推荐开放平台
function ginfo_ding_activities(id, type, element) {

    var tip_element = $(element).nextAll('.layer_sync');
    //检查用户是否设置同步
    check_connect_setting(5, 'sina', function() { //未设置
        //检查周期内是否提示过
        check_connect_remind('sina', 'ginfo_ding', function() { //未提示过
            tip_element.find('.pop_wrap[key="remind"]').find('.close, .operation .sync_cancel').unbind('click').bind('click', function() {
                tip_element.find('.pop_wrap[key="remind"]').fadeOut(300);
            }).end().find('.operation .sync_submit').unbind('click').bind('click', function() {
                tip_element.find('.pop_wrap[key="remind"]').fadeOut(300);
                //设置同步
                set_connect_setting(5, 'sina');
                //请求接口
                update_activities(id, type, 'ginfo_ding', function() {
                    tip_element.find('.pop_wrap[key="result"]').html('<div class="succSync"><b></b>推荐成功！</div><p>你可以通过窝内的<a href="/home/usershare.php?show_id=sina_setting" target="_blank">账号同步</a>更改同步设置。</p>').fadeIn(300);
                    setTimeout(function() {
                        tip_element.find('.pop_wrap[key="result"]').fadeOut(300);
                    }, 5000);
                    //统计发送动态数
                    add_op_log('sgda', 'sendsuccess');
                }, function() {
                    tip_element.find('.pop_wrap[key="result"]').html('<div class="succSync"><b></b>抱歉，推荐失败，请稍候再试！</div>').fadeIn(300);
                    setTimeout(function() {
                        tip_element.find('.pop_wrap[key="result"]').fadeOut(300);
                    }, 5000);
                });
                //统计开启同步数
                add_op_log('sgda', 'openservice');
            }).end().fadeIn(300);
            //统计弹窗数
            add_op_log('sgda', 'remindtip');
        });
    }, function() { //已设置
        //请求接口
        update_activities(id, type, 'ginfo_ding', function() {
            //统计发送动态数
            add_op_log('sgda', 'sendsuccess');
        });
    });
}

//绑定手机号
function invite_bang(){

    $.post("/login/invite_friend.php",{action:"bang"},function(d){
        if(d.ret==1){
            if(d.msg==1){
                todo_1(d.html);
            }else{
                todo_2(d.html);
            }
        }else{
            majaxerr(d);
        }
    },"json");
}

function todo_1(html){
    $.prompt(html,{buttons:{"提交":true},submit:function(v,m,f){
        if(v){
            var _name = $("#true_name",$(m)).val();
            var _mobile = $("#invite_mobile",$(m)).val();
            var _invite_code = $("#invite_code",$(m)).val();
            $.post("/login/invite_friend.php", {action:"verify_code", true_name:_name, mobile:_mobile,invite_code:_invite_code},
                function(d){
                    if(d.ret==1){
                        invite_bang();
                        $.prompt.close();
                    }else{
                        $("#err_msg").html(d.msg);
                        //majaxerr(d);
                    }
                },"json");
        }
        return false;
    }});
}

function todo_2(html){
    $.prompt(html,{buttons:{"确认邀请":true},submit:function(v,m,f){
        if(v){
            var _name1=$("#invite_name1",$(m)).val();
            var _name2=$("#invite_name2",$(m)).val();
            var _name3=$("#invite_name3",$(m)).val();

            var _mobile1=$("#invite_mobile1",$(m)).val();
            var _mobile2=$("#invite_mobile2",$(m)).val();
            var _mobile3=$("#invite_mobile3",$(m)).val();

            if("姓名1" == _name1){
                _name1 = "";
            }

            if("姓名2" == _name2){
                _name2 = "";
            }

            if("姓名3" == _name3){
                _name3 = "";
            }

            if("手机号1" == _mobile1){
                _mobile1 = "";
            }

            if("手机号2" == _mobile2){
                _mobile2 = "";
            }

            if("手机号3" == _mobile3){
                _mobile3 = "";
            }

            $.post("/login/invite_friend.php",
                {action:"invite",name1:_name1,name2:_name2,name3:_name3,mobile1:_mobile1,mobile2:_mobile2,mobile3:_mobile3},
                function(d){

                    if(d.ret==1){
                        mTinyAlert("操作成功！");
                        $.prompt.close();
                    }else if(d.ret == 10){
                        var msg = d.msg;
                        err_arr = msg.split(",");
                        var key =0;
                        for(i=0; i<err_arr.length; i++){
                            key = err_arr[i];
                            if(!$("#li_A"+key).hasClass("on")&&!$("#li_A"+key).hasClass("off")){
                                $("#li_A"+key).removeClass();
                                $("#li_A"+key).addClass("A"+key+" err");
                            }
                        }

                        return false;
                    } else {
                        majaxerr(d);
                        $.prompt.close();
                    }
                },"json");
        }
        return false;
    }
    });
}

//页面加载完成加载方法
jQuery(function(){
    var pagepara="start";
    //获取内容
    function getC(_url){
        //jQuery("#div_loadingpanel").show();
        jQuery.post(_url,{type:jQuery("#smallpager").data("type")},function(d){
            if(d.ret==1){
                jQuery("#pnl_content").fadeOut(700,function(){
                    jQuery(this).html(d.html).show();
                    jQuery("html,body").animate({scrollTop: jQuery("#pnl_btns").offset().top}, 1500);
                });
                updatePager(d.nowpage,d.recordcount,d.pagesize);
                jQuery("#div_loadingpanel").hide();
            }
            else
            {
                majaxerr(d);
            }
        },"json");
    }
    //更新分页
    function updatePager(page,rnum,psize){
        jQuery("#smallpager").html("").Pager({config:"index",recordcount: rnum,pagesize: psize,nowpage: page,url: "/ajax/ajax_article.php",pageparameter:pagepara})
            .find("a").click(function(){
                var _url=jQuery(this).attr("href");
                if(_url.indexOf(pagepara)>0)
                {

                    getC(_url);
                }
                return false;
            });
    }
    //btn绑定 begin
    jQuery("#btn_tp0").click(function(){
        jQuery("#pnl_btns li.on").removeClass("on");
        jQuery(this).parent().addClass("on");
        jQuery("#smallpager").data("type","0");
        getC("/ajax/ajax_article.php?"+pagepara+"=1");
    }).focus(function(){jQuery(this).blur();});
    jQuery("#btn_tp1").click(function(){
        jQuery("#pnl_btns li.on").removeClass("on");
        jQuery(this).parent().addClass("on");
        jQuery("#smallpager").data("type","1");
        getC("/ajax/ajax_article.php?"+pagepara+"=1");
    }).focus(function(){jQuery(this).blur();});
    jQuery("#btn_tp2").click(function(){
        jQuery("#pnl_btns li.on").removeClass("on");
        jQuery(this).parent().addClass("on");
        jQuery("#smallpager").data("type","2");
        getC("/ajax/ajax_article.php?"+pagepara+"=1");
    }).focus(function(){jQuery(this).blur();});
    jQuery("#btn_tp3").click(function(){
        jQuery("#pnl_map").toggle();
    }).focus(function(){jQuery(this).blur();});
    //jQuery('#pnl_map').bind("mouseleave",function(){jQuery(this).hide();});
    //btn绑定 end

    updatePager(1,1000,15);
    jQuery("#smallpager").data("type","0");


//    if(_IS_LOGIN_ =="0"){
//        if(!$.cookie("mmfw_open", {path:'/'})) {
//            $("#mmfw-open").show().delegate('a.close','click', function (e) {
//                $.cookie("mmfw_open", 1, { expires: 1, path: '/', domain: 'mafengwo.cn'});
//                e.preventDefault();
//                $("#mmfw-open").remove();
//            });
//        }
//    }

    if(!$.cookie("xcuxm", {path:'/'})) {
        $("<img/>").attr("src","http://www.agoda.com.cn/?cid=1603926");
        $.cookie("xcuxm", 1, { expires: 2, path: '/', domain: 'mafengwo.cn'});
    }

    function getPostalCardHtml(){
        $.post("/activity/ajax_postal_action.php",{ 'action' : 'indexleft' },function(d){
            if(d.ret==1){
                $("div._j_postalcard").html(d.html).show();
            }
        },"json");
    }
    getPostalCardHtml();
});