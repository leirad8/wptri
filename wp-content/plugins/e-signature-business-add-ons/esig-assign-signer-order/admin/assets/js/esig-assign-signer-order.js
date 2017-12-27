(function ($) {



    // default is disable 
    
       $('body').on('click', '#document_form > div.esign-form-panel.basic_esign #esign-assign-signer-order', function (e) {  
          
          $(".invitations-container a").trigger('click');
      });

    $("#addRecipient_view").on("click", function (e) {
        
        $('#esig-view-signer-add #esign-signer-order-show').fadeIn(1600, "linear");
    });

    $('body').on('click', '#esig-signer-edit-wrapper .add-signer', function (e) {
       
        $('#esig-signer-edit-wrapper #esign-signer-order-show').fadeIn(1600, "linear");

    });
    
    // Show or hide signer order 
    //$('input[name="esign-assign-signer-order"]').on('change', function () {
     $('body').on('change', '#esig-signer-edit-wrapper #esign-assign-signer-order', function (e) {
        if ($('#esig-signer-edit-wrapper #esign-assign-signer-order').attr('checked')) {
            $.fn.signer_order_checked();
        } else {
            $.fn.signer_order_unchecked();
        }
    });


    $('body').on('change', '#esig-view-signer-add #esign-assign-signer-order', function (e) {

        if ($('#esig-view-signer-add #esign-assign-signer-order').attr('checked')) {
            $.fn.signer_order_checked_view();
        } else {
            $.fn.signer_order_unchecked_view();
        }

    });


    $('input[name="esign-assign-signer-order-ajax"]').on('change', function () {

        if ($('input[name="esign-assign-signer-order-ajax"]').attr('checked')) {
            $.fn.signer_order_checked_view();
        } else {
            $.fn.signer_order_unchecked_view();
        }

    });
    
    
    $('input[name="esign-assign-signer-order-temp"]').on('change', function () {

        if ($('input[name="esign-assign-signer-order-temp"]').attr('checked')) {
            $.fn.signer_order_checked_temp();
        } else {
            $.fn.signer_order_unchecked_temp();
        }

    });
    

    // signer order checked funciton 
    $.fn.signer_order_checked = function () {
        
        

        var fname = $("#esig-signer-edit-wrapper input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_name = $("#esig-signer-edit-wrapper input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });
        
        var html = '';
         var delicon = '';
         var slv = '';
            if (esign.is_slv_active()) {
                slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
            }
        for (i = 0; i < fname.length; i++) {

            var j = i + 1;
            
            if (j == '1') {
                delicon = '' + slv;
            } else {
                delicon = slv + '<span id="esig-del-signer" class="deleteIcon"></span>';
            }
            html += '<div id="signer_main">' +
                    '<span id="signer-sl" class="signer-sl">' + j + '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span>' +
                    '<input type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' + fname[i] + '" />' +
                    '<input type="text" name="recipient_emails[]" class="recipient-email-input" placeholder="email@address.com"  value="' + signer_name[i] + '" />' + delicon + '</div>';

        }
        
        $('#esig-signer-edit-wrapper #recipient_emails').html(html);

    }
    // for view 
    $.fn.signer_order_checked_view = function () {

        var fname = $("#recipient_emails input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_name = $("#recipient_emails input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';
        var delicon = '';

        for (i = 0; i < fname.length; i++) {
            var j = i + 1;

            var slv = '';
            if (esign.is_slv_active()) {
                slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
            }
            if (j == '1') {
                delicon = '' + slv;
            } else {
                delicon = slv + '<span id="esig-del-signer" class="deleteIcon"></span>';
            }

            html += '<div id="signer_main">' +
                    '<span id="signer-sl" class="signer-sl">' + j + '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span>' +
                    '<input type="text"  name="recipient_fnames[]" placeholder="Signers Name" value="' + fname[i] + '" />' +
                    '<input type="text" name="recipient_emails[]" placeholder="email@address.com" style="width:190px;"  value="' + signer_name[i] + '" />' + delicon + '</div>';


        }

        $('#recipient_emails').html(html);

    }


    // signer order unchecked funciton 
    $.fn.signer_order_unchecked = function () {

        var fname = $("#esig-signer-edit-wrapper input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_name = $("#esig-signer-edit-wrapper input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';
        var delicon = '';
         var slv = '';
            if (esign.is_slv_active()) {
                slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
            }
            
        for (i = 0; i < fname.length; i++) {
            var j = i + 1;
            if (j == '1') {
                delicon = '' + slv;
            } else {
                delicon = slv + '<span id="esig-del-signer" class="deleteIcon"></span>';
            }
            html += '<div id="signer_main">' +
                    '<input type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' + fname[i] + '" />' +
                    '<input type="text" name="recipient_emails[]" class="recipient-email-input" placeholder="Signer Email"  value="' + signer_name[i] + '" />' + delicon + '</div>';

        }

        $('#esig-signer-edit-wrapper #recipient_emails').html(html);

    }

    $.fn.signer_order_unchecked_view = function () {

        var fname = $("#recipient_emails input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_name = $("#recipient_emails input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';
        
        var slv = '';
            if (esign.is_slv_active()) {
                slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
            }

        for (i = 0; i < fname.length; i++) {
            var j = i + 1;
            if (j == '1') {
                delicon = '' + slv;
            } else {
                delicon = slv + '<span id="esig-del-signer" class="deleteIcon"></span>';
            }
            html += '<div id="signer_main">' +
                    '<input type="text" name="recipient_fnames[]" placeholder="Signers Name" value="' + fname[i] + '"  />' +
                    '<input type="text" name="recipient_emails[]" placeholder="email@address.com"  style="width:235px;"  value="' + signer_name[i] + '"  />' + delicon + '</div>';

        }

        $('#recipient_emails').html(html);

    }
    
    //assign signer order for templete
    
      $.fn.signer_order_checked_temp = function () {
          
        

        var fname = $("#recipient_emails_temp input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_name = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';
        var delicon = '';

        for (i = 0; i < fname.length; i++) {
            var j = i + 1;

            var slv = '';
            if (esign.is_slv_active()) {
                slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
            }
            if (j == '1') {
                delicon = '' + slv;
            } else {
                delicon = slv + '<span id="esig-del-signer" class="deleteIcon"></span>';
            }

            html += '<div id="signer_main_pop">' +
                    '<span id="signer-sl" class="signer-sl">' + j + '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span>' +
                    '<input type="text"  name="recipient_fnames[]" placeholder="Signers Name" value="' + fname[i] + '" />' +
                    '<input type="text" name="recipient_emails[]" placeholder="Signer Email" style="width:190px;"  value="' + signer_name[i] + '" />' + delicon + '</div>';


        }

        $('#recipient_emails_temp').html(html);

    }


    // signer order unchecked funciton 
    $.fn.signer_order_unchecked_temp = function () {

        var fname = $("#recipient_emails_temp input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_name = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';
        var delicon = '';
         var slv = '';
            if (esign.is_slv_active()) {
                slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
            }
            
        for (i = 0; i < fname.length; i++) {
            var j = i + 1;
            if (j == '1') {
                delicon = '' + slv;
            } else {
                delicon = slv + '<span id="esig-del-signer" class="deleteIcon"></span>';
            }
             html += '<div id="signer_main_pop">' +
                    '<input type="text"  name="recipient_fnames[]" placeholder="Signers Name" value="' + fname[i] + '" />' +
                    '<input type="text" name="recipient_emails[]" placeholder="Signer Email" style="width:190px;"  value="' + signer_name[i] + '" />' + delicon + '</div>';

        }

        $('#recipient_emails_temp').html(html);

    }
    

    // when js load checking signer order checked or not if checked then show order
    if ($('input[name="esign-assign-signer-order"]').attr('checked')) {

        $.fn.signer_order_checked();
    }

    $('body').on('click', '#recipient_emails .deleteIcon', function () {

        // checking if signer only one then hide signer order checkbox 

        $(this).parent().remove();
        var fname = $("#recipient_emails input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });
        if (fname.length == 1) {
            $('#esign-signer-order-show').fadeOut(1600, "linear");
            $('.esig-signer-left #esign-signer-order-show').hide();
            $('.field_arrows').hide();
            $('.signer-sl').hide();
        }
        return false;
    });

    // esign signer order up down click event code here 
    $('body').on('click', '#esig_signer_up', function () {

        var current = $(this).parent().parent().find('#signer-sl').html();

        var upper = $(this).parent().parent().prev().find("#signer-sl:first").html();

        if (upper == undefined) {
            return;
        }

        // setting upper and current
        var parent = $(this).parent().parent();
        parent.animate({top: '-20px'}, 500, function () {
            parent.prev().animate({top: '20px'}, 500, function () {
                parent.css('top', '0px');
                parent.prev().css('top', '0px');
                parent.insertBefore(parent.prev());
            });
        });

        $(this).parent().parent().prev().find("#signer-sl:first").html(current);
        $(this).parent().parent().find('#signer-sl').html(upper);



    });

    $('body').on('click', '#esig_signer_down', function () {

        // getting current and down value 
        var current = $(this).parent().parent().find('#signer-sl').html();
        var down = $(this).parent().parent().next().find("#signer-sl:first").html();

        if (down == undefined) {
            return;
        }
        // setting current and next value 
        var parent = $(this).parent().parent();
        parent.animate({top: '20px'}, 500, function () {
            parent.next().animate({top: '-20px'}, 500, function () {
                parent.css('top', '0px');
                parent.next().css('top', '0px');
                parent.insertAfter(parent.next());
            });
        });
        $(this).parent().parent().find('#signer-sl').html(down);
        $(this).parent().parent().next().find("#signer-sl:first").html(current);


    });

    // changing add reciepent button in view page  . 
    $('#recipient_emails').bind("contentchange", function () {
        if ($('input[name="esign-assign-signer-order-view"]').attr('checked')) {
            $.fn.signer_order_checked_view();
        }
    });

})(jQuery);