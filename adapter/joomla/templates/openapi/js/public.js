(function($){
  $(function(){
    $("ul#myTab a,ul#product").live("click",function(){
        if($(this).parents("li").hasClass("active")) return false;
        $(this).parents("li").addClass("active").siblings().removeClass("active");
        var sel = $(this).attr("href");
        $(sel).show().siblings().hide();
        return false;
    });
    $("ul#product").parents("div").find("#overview").addClass("active");
    $("input#login-action-button").live("click",function(){
      var login_form_show = $("#login-form:visible").length;
      if(login_form_show){
        $("#login-form").slideUp();
      }else{
        $("#login-form").slideDown();
      }
      return false;
    });

    $("#login-form").live("submit",loginBackend);
    $("#login-page-login-form").live("submit",loginBackend)
    function loginBackend(){
      if($(".logout-button").length){return true;}
      
      $that     = $(this);
      $id       = $that.attr("id");
      $username = $that.find("input[name='username']").val();
      $passwd   = $that.find("input[name='password']").val();
      $.get(GLOBAL_CONTEXT_PATH + "administrator/index.php",function(res){
        var data = {};
        var rform = $(res).find("#form-login");
            data[$("input[value='1']",rform).attr("name")] = 1;
            data.username = $username;
            data.passwd = $passwd;
            data.option = $("input[name='option']",rform).val();
            data.task = $("input[name='task']",rform).val();

        $.post(GLOBAL_CONTEXT_PATH + "administrator/index.php",data,function(d){
            $("#" + $id).die("submit").submit();
        });
      });

      return false;
    }
  });
  //Commenting the code as the help text doesnot look good if the help text is very long.
  $(document).ready(function(){
//    jQuery("span.pull-right[style*='help']").each(function(){
//      var helpText = $(this).attr("data-original-title");
//    //  $(this).after('<span class="pull-right" style="font-size:9px; position: absolute; margin-top: 20px; width: 90px;">'+ helpText +'</span>'); 
//      //$(this).after('<span class="pull-right" style="position: absolute; margin-top: 20px;">'+ helpText +'</span>');
//    });
    jQuery("form").on("focus", "input", function(e) {
      var _dom = jQuery(this);
      var _val = _dom.val();
      setTimeout(function(){
        _dom.val(_val);
      },0);
    });
    jQuery("input.threshold").height("18").on("keyup", function(i, e){
      var val = jQuery(this).val();
      if (val >= 100) {
        jQuery(this).val("100");
      }
    });
  });
})(jQuery);