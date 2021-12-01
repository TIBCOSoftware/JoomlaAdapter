/*
 * @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

(function($) {
    $("#support_area form button").live("click", function (){
        var textLength = $("#support_area").find("form textarea").val().trim().length,
            emailPattern = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,
            email = $('input[name="email"]').val(),
            fname = $('input[name="fname"]').val(),
            fname = fname===$('input[name="fname"]')[0].defaultValue?'':fname;

        if (fname==='') {
            $('input[name="fname"]').val('User');
        }

        if (!emailPattern.test(email)) {
            Joomla.showError([SUPPORT_INPUT_EMAIL]);
            return false;
        }

        if(textLength>0){
            Joomla.removeMessages();
            $("#support_area").find("form button.btn").attr("disabled",true);
            $("#support_area").find("div#msg_box").remove();
            $.post(
                GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.requestSupport",
                $("#support_area form").serialize(),
                function(data){
                    if (data.success) {
                        Joomla.showSuccess(["Support email sent successfully!"]);

                        $("#support_area").find("form textarea").val("");
                        // $("#support_area").find("form input[type='text']").val('');
                        grecaptcha.reset();
                        $("#support_area").find("form button.btn").attr("disabled",false);
                    }else{
                        Joomla.showError([data.error]);
                        $("#support_area").find("form button.btn").attr("disabled",false);
                    }
                },
                'json'
            );
        } else{
            Joomla.showError([SUPPORT_INPUT_QUESTION]);
            return false;
        }

        return false;
    });


    $(document).ready(function(){
        // this is designed for the support page only, but is loaded for all pages, causing problems in other forms
        // the following IF condition solves this problem, bur requires that "Support" or "support" be in the
        // title of url of the page
        if((this.title && ((this.title.indexOf("Support") >= 0) ||
            (this.title.indexOf("support") >= 0))) ||
            (this.documentURI && ((this.documentURI.indexOf("Support") >= 0) ||
                (this.documentURI.indexOf("support") >= 0)))) {

            //insert captcha
            $.get(
                GLOBAL_CONTEXT_PATH+"index.php?option=com_cobalt&task=ajaxMore.requestSupportInit",
                function(data){
                    if (data.success) {
                        $("[name='content']").after("<p>Captcha *</p><div id='jform_captcha' class='required g-recaptcha' data-sitekey='"+data.sitekey+"'>Please wait, Loading Captcha...</div>"
                        );
                        $.getScript( "https://www.google.com/recaptcha/api.js",function(res, status){

                        });
                    }else{
                        Joomla.showError([data.error]);
                        $("#support_area").find("form button.btn").attr("disabled",true);
                    }
                },
                'json'
            );

            $('input[name="fname"]').css("width","40%").css("margin-right","10px");
            $('input[name="email"]').css("width","74%");
            if (SUPPORT_USER_EMAIL.length>0) {
                $('input[name="email"]').val(SUPPORT_USER_EMAIL).hide();
                $('input[name="email"]').prev("p").hide();
                $('input[name="fname"]').val(SUPPORT_USER_NAME);
            }
            if (SUPPORT_USER_NAME.length>0) {
                $('#u_name_span').html(SUPPORT_USER_NAME);
                $('input[name="fname"]').hide();
                $('input[name="fname"]').prev("p").hide();
            }

            // parse the parameters in the URL to decide whether to pre-populate the query field or not
            var sSearch = window.location.search,
                aParameters = sSearch ? sSearch.substring(1).split('&') : [],
                bPrepopulate = false,
                sUUID, i, aKV, sK, sV,
                fPrepopulate = function(sUUID) {
                    var dQuery = $('textarea[name="content"]');
                    dQuery.val(SUPPORT_PREPOPULATED_TEXT_UUID.replace(/<br\s*\/>/g, '\n\n').replace(/\{uuid\}/, sUUID));
                };
            for(i = 0; i < aParameters.length; i++) {
                aKV = aParameters[i].split('=');
                sK = aKV[0]; sV = aKV[1];
                if(sK === 'prepopulate' && sV === '1') {
                    bPrepopulate = true;
                } else if(sK === 'uuid') {
                    sUUID = sV;
                }
            }

            if(bPrepopulate && sUUID) {
                fPrepopulate(sUUID);
            }
        }

    });
}(jQuery));
