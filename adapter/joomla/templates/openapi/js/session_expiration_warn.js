(function(){
    var _html = '<div id="full_bg"></div>' +
        '<div id="sessionDialog">' +
        '<p class="modal-header" style="border-radius: 5px; text-align: right;"><a id="endActive" style="cursor: pointer;">Close</a></p>' +
        '<div class="modal-body">' +
            '{session_expiration_warn_info}'+
        '</div>' +
        '<p class="modal-footer"><button id="keepActive" type="button">OK</button></p>' +
        '</div>';

    jQuery('body').append(_html);
    jQuery('#full_bg').css({
        'background-color': 'gray',
        'position': 'fixed',
        'top': 0,
        'left': 0,
        'opacity': '0.5',
        'filter': 'alpha(opacity=50)',
        '-moz-opacity': '0.5',
        '-ms-opacity': '0.5',
        '-webkit-opacity': '0.5',
        '-o-opacity': '0.5',
        'width': '100%',
        'height': '100%',
        'z-index': '5000'
    });

    jQuery('#sessionDialog').css({
        'position': 'fixed',
        'top': '50%',
        'left': '50%',
        'height': '300px',
        'width': '400px',
        'background-color': '#fff',
        'margin': '-200px 0 0 -150px',
        'z-index': '6000',
        'border-radius': '5px',
        'display': 'block'
    });

    jQuery('#endActive').on('click', function(){
        jQuery('#sessionDialog,#full_bg').hide();
    });
    jQuery('#keepActive').on('click', function(){
        jQuery.ajax({
            url: './',
            type: 'get'
        });

        jQuery('#sessionDialog,#full_bg').hide();
    });
    var date = new Date();
    date.setMinutes(date.getMinutes()+2);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var d = date.getDate();
    var hour = date.getHours();
    var minute = date.getMinutes();
    jQuery('.expireDate').text(year + '-' + month + '-' + d + ' ' + hour + ':' + minute);
})


