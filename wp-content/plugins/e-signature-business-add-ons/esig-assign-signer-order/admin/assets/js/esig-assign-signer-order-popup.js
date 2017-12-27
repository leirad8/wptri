(function ($) {


    $('input[name="esig_assign_approval_signer"]').on('change', function () {

        if ($('input[name="esig_assign_approval_signer"]').attr('checked')) {
            tb_show("", '#TB_inline?width=500&height=300&inlineId=approval_signer_view_popup');
            $('input[name="esig_assign_approval_signer"]').prop('checked', false);
        }

    });


    // adding approval signer 
    $("#add-approval-signer").on("click", function (e) {
        e.preventDefault();

        $("#recipient_approval_signer").append('<div id="signer_main">' +
                '<input type="text" name="approval_signer_fname[]" placeholder="Signers Name"  />' +
                '<input type="text" name="approval_signer_emails[]" placeholder="email@address.com" style="width:225px;"  value="" /><span id="esig-del-approval-signer" class="deleteIcon"></span></div>').trigger("contentchange");


        // count approval signer emails if it gretar than 2 signer order option will be visible . 
        var signer_email_array = $("#recipient_approval_signer input[name='approval_signer_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        if (signer_email_array.length > 2) {

            $('#esign-approval-signer-order').fadeIn(1600, "linear");
        }
    });

    // removing approval signer 
    $('body').on('click', '#recipient_approval_signer .deleteIcon', function (e) {


        // removing signer 
        $(this).parent().remove();

        // 
        //$(this).remove();
        // if signer removes and signer toal is less than 2 hide signer order . 

        var signer_email_array = $("#recipient_approval_signer input[name='approval_signer_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        if (signer_email_array.length < 3) {

            $('#esign-approval-signer-order').fadeOut(1600, "linear");
        }
        e.preventDefault();
    });



    $('input[name="esign_assign_approval_signer_order"]').on('change', function () {

        if ($('input[name="esign_assign_approval_signer_order"]').attr('checked')) {

            $.fn.approval_signer_order_checked();
        } else {
            $.fn.approval_signer_order_unchecked();
        }

    });



    // adding function to sort signer 
    // signer order checked funciton 
    $.fn.approval_signer_order_checked = function () {

        var fname = $("input[name='approval_signer_fname\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_emails = $("input[name='approval_signer_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';



        for (i = 0; i < fname.length; i++) {

            var j = i + 1;
            if (i == 0) {

                html += '<div id="signer_main" style="margin-left:20px;">' +
                        '<span id="signer-se" class="signer-se" >' + j + '.</span>' +
                        '<input type="text" name="approval_signer_fname[]" placeholder="Signer 1(from website)" value="" readonly />' +
                        '<input type="text" name="approval_signer_emails[]" placeholder="Signer 1(from website)" style="width:191px;"  value="" readonly /></div>';
            } else {

                html += '<div id="signer_main">' +
                        '<span id="signer-sl" class="signer-sl">' + j + '.</span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span>' +
                        '<input type="text" name="approval_signer_fname[]" placeholder="Signers Name" value="' + fname[i] + '" />' +
                        '<input type="text" name="approval_signer_emails[]" placeholder="Signer Email" style="width:194px;"  value="' + signer_emails[i] + '"  /><span id="esig-del-approval-signer" class="deleteIcon"></span></div>';
            }

        }


        $('#recipient_approval_signer').html(html);

    }


    // signer order unchecked funciton 
    $.fn.approval_signer_order_unchecked = function () {

        var fname = $("input[name='approval_signer_fname\\[\\]']").map(function () {
            return $(this).val();
        });

        var signer_emails = $("input[name='approval_signer_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var html = '';

        for (i = 0; i < fname.length; i++) {
            var j = i + 1;


            if (i == 0) {

                html += '<div id="signer_main">' +
                        '<input type="text" name="approval_signer_fname[]" placeholder="Signer 1(from website)" value="" readonly />' +
                        '<input type="text" name="approval_signer_emails[]" placeholder="Signer 1(from website)" style="width:221px;"  value="" readonly /></div>';
            } else {


                html += '<div id="signer_main">' +
                        '<input type="text" name="approval_signer_fname[]" placeholder="Signers Name" value="' + fname[i] + '"  />' +
                        '<input type="text" name="approval_signer_emails[]" placeholder="Signer Email" style="width:224px;"  value="' + signer_emails[i] + '" /><span id="esig-del-approval-signer" class="deleteIcon"></span></div>';
            }
        }

        $('#recipient_approval_signer').html(html);

    }

    // email validation checking on basic document add view . 

    $.fn.approval_email_duplicate = function () {

        var view_email = $("#recipient_approval_signer input[name='approval_signer_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var view_fname = $("#recipient_approval_signer input[name='approval_signer_fname\\[\\]']").map(function () {
            return $(this).val();
        });

        var sorted_email = view_email.sort();
        // getting new array 
        var exists = false;
        var blank = false;
        var blank_email = false;

        if (view_fname.length < 2) {
            $('.esig-error-box').remove();
            $('#recipient_approval_signer').parent().after('<div class="esig-error-box">*Please input at least one approval signer name and e-mail address</div>');
            return true;
        }
        else {
           $('.esig-error-box').remove(); 
        }

        // if blank signer name is input 
        for (var i = 1; i < view_fname.length; i++) {



            if (view_fname[i] == undefined || view_fname[i] == '')
            {

                blank = true;
            }

            // var regexp = new RegExp(/^[a-z]([-']?[a-z]+)*( [a-z]([-']?[a-z]+)*)+$/i);
            if (!esign.isFullName(view_fname[i])) {


                blank = true;
            }


            var re = /<(.*)>/
            if (re.test(view_fname[i]))
            {
                blank = true;
            }

            if (blank)
            {
                $('.esig-error-box').remove();
                $('#recipient_approval_signer').parent().after('<div class="esig-error-box">*A Full name including your first and last name is required to send this contract.</div>');
                return true;
            }
        }
        // if blank email address is input 
        for (var i = 1; i < view_email.length; i++) {

            if (view_email[i] == undefined || view_email[i] == '')
            {
                blank_email = true;
            }


            if (!esign.is_valid_email(view_email[i]))
            {
                blank_email = true;
            }
            if (blank_email)
            {
                // remove previous error msg 
                $('.esig-error-box').remove();
                // add new error msg 
                $('#recipient_approval_signer').parent().after('<div class="esig-error-box">*You must fill email address.</div>');
                return true;
            }
        }


        for (var i = 1; i < view_email.length - 1; i++) {

            if (sorted_email[i + 1].toLowerCase() == sorted_email[i].toLowerCase())
            {
                exists = true;
            }
        }

        if (exists)
        {

            $('.esig-error-box').remove();

            $('#recipient_approval_signer').parent().after('<span class="esig-error-box"> *You can not use duplicate email address.</span>');

            return true;
        }
        else
        {
            $('.esig-error-box').remove();
            return false;
        }

    }

    //check validatioin on input change 
    $('body').on('focusout', "#recipient_approval_signer input[name='approval_signer_emails\\[\\]']", function () {

        // checking if signer only one then hide signer order checkbox 
        $.fn.approval_email_duplicate();


    });


    $('#submit_approval_signer_save').click(function () {

        // validation for same email address . 
        if ($.fn.approval_email_duplicate())
        {
            return false;
        }

        var esig_approval_signer_fname = '';
        var esig_approval_signer_email = '';

        esig_approval_signer_fname = $("input[name='approval_signer_fname\\[\\]']").map(function () {
            return $(this).val();
        }).get();
        esig_approval_signer_email = $("input[name='approval_signer_emails\\[\\]']").map(function () {
            return $(this).val();
        }).get();

        var signer_order = '';

        if ($('input[name="esign_assign_approval_signer_order"]').attr('checked')) {

            signer_order = 'active';

        } else {
            signer_order = 'inactive';
        }
        
        if (esig_approval_signer_email.leanth < 2) {
            return false;
        }

        var esig_document_id = $('input[name="document_id"]');




        var data = {
            'approval_signer_fname': esig_approval_signer_fname,
            'approval_signer_emails': esig_approval_signer_email,
            'document_id': esig_document_id.val(),
            'esign_assign_approval_signer_order': signer_order,
        };
        jQuery.post(sadmyAjax.ajaxurl + "?action=esig_assign_approval_signer", data, function () {

            tb_remove();
            // check and enable approval signer order feature . 
            $('input[name="esig_assign_approval_signer"]').prop('checked', true);
        });
        return false;
    });






})(jQuery);
