(function ($) {


    $("#add-esig-cc").on("click", function (e) {
        e.preventDefault();

        $(".cc_recipient_emails_container #cc_recipient_emails").append('<div id="recipient_emails" style="position:relative;">' +
                '<input type="text" name="cc_recipient_fnames[]" placeholder="CC Users Name" style="height:36px;" />' +
                '<input type="text" name="cc_recipient_emails[]" placeholder="email@address.com" style="width:235px;height:36px;"  value="" /><span id="esig-del-signer" class="deleteIcon" ></div>').trigger("contentchange");

    });

    $("#cc_users_edit").on("click", function (e) {
        e.preventDefault();

        $("#cc_recipient_emails").append('<div id="signer_main" style="position:relative; left:6px;">' +
                '<input type="text" name="cc_recipient_fnames[]" placeholder="CC Users Name" style="height:35px;" />' +
                '<input type="text" name="cc_recipient_emails[]" placeholder="email@address.com" style="width:214px;height:35px;"  value="" /><span id="esig-del-signer" class="deleteIcon" style="position:absolute;left:400px;"></div>').trigger("contentchange");
    });


    $("#add_cc_temp").on("click", function (e) {
        e.preventDefault();

        $(".invitations-container .cc_recipient_emails").append('<div id="cc-signer_main" class="cc-invitation-email">' +
                '<input type="text" name="cc_recipient_fnames[]" placeholder="CC Users Name" style="height:35px;" />' +
                '<input type="text" name="cc_recipient_emails[]" placeholder="email@address.com" style="width:213px;height:35px;"  value="" /><span id="esig-del-signer" class="deleteIcon" style="position:absolute;left:400px;"></div>').trigger("contentchange");

    });

    //$("#esig-signer-edit-wrapper #add_cc").on("click", function (e) {
    $('body').on('click', '#esig-signer-edit-wrapper #add_cc', function (e) {
        e.preventDefault();

        $("#esig-signer-edit-wrapper #cc_recipient_emails12").append('<div id="esig-signer-edit-wrapper" class="cc-invitation-email">' +
                '<input type="text" name="cc_recipient_fnames[]" placeholder="CC Users Name" style="height:37px;border-radius:3px;" />' +
                '<input type="text" name="cc_recipient_emails[]" placeholder="email@address.com" style="width:238px;height:37px;border-radius:3px;"  value="" /><span id="esig-del-signer" class="deleteIcon" style="position:absolute;left:420px;"></div>').trigger("contentchange");

    });

    $('body').on('click', '.esig_nextstep #submit_send', function (e) {

        // validation for same email address . 
        if ($.fn.cc_users_email_duplicate('#cc_recipient_emails', '.cc_recipient_emails_container #error'))
        {
            return false;
        }
        else
        {
            // saving removed any error msg 
            $('.cc_recipient_emails_container .esig-error-box').remove();
        }
        return true;


    });



    // temp cc users 
    $("#standard_view_popup_bottom #submit_insert").on("click", function (e) {
        // $('#template_insert').click(function () {


        // validation for same email address . 
        if ($.fn.cc_users_email_duplicate('#cc_recipient_emails', "#standard_view_popup_bottom #error"))
        {
            
            return false;
        }
        else
        {
            // saving removed any error msg 
            $('#standard_view_popup_bottom .esig-error-box').remove();
        }

        var esig_cc_recipient_fnames = '';
        var esig_cc_recipient_emails = '';

        esig_cc_recipient_fnames = $(".invitations-container input[name='cc_recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        }).get();
        esig_cc_recipient_emails = $(".invitations-container input[name='cc_recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        }).get();

        var esig_document_id = $('input[name="document_id"]');

        var data = {
            'cc_recipient_fnames': esig_cc_recipient_fnames,
            'cc_recipient_emails': esig_cc_recipient_emails,
            'document_id': esig_document_id.val(),
        };

        $.post(esigAjax.ajaxurl + "?action=esig_cc_user_information", data).done(function (response) {


        });
        //return false;

    });





    // email validation checking on basic document add view . 

    $.fn.cc_users_email_duplicate = function (esigSelection, esigError) {

        var view_email = $(esigSelection + " input[name='cc_recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        }).get();

        var view_fname = $(esigSelection + " input[name='cc_recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        }).get();

        var sorted_email = view_email.sort();

        // getting new array 
        var exists = false;
        var blank = false;
        var blank_email = false;
        // if blank signer name is input 
        for (var i = 0; i < view_fname.length; i++) {

            if (view_fname[i] == undefined || view_fname[i] == '')
            {

                blank = true;
            }

            var re = /<(.*)>/
            if (re.test(view_fname[i]))
            {
                blank = true;
            }

           // var regexp = new RegExp(/^[a-z]([-']?[a-z]+)*( [a-z]([-']?[a-z]+)*)+$/i);
            if (!esign.isFullName(view_fname[i])) {
                
                blank = true;
            }
            
            if (blank)
            {

                $('.esig-error-box').remove();
                $(esigError).append('<span class="esig-error-box">*A Full name including your first and last name is required.</span>');
                return true;
            }
        }
        // if blank email address is input 
        for (var i = 0; i < view_email.length; i++) {

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
                $(esigError).append('<span class="esig-error-box">*You must fill CC email address.</span>');
                return true;
            }
        }


        for (var i = 0; i < view_email.length - 1; i++) {

            if (sorted_email[i + 1].toLowerCase() == sorted_email[i].toLowerCase())
            {
                exists = true;
            }
        }

        if (exists)
        {

            $('.esig-error-box').remove();

            $(esigError).append('<span class="esig-error-box"> *You can not use CC duplicate email address.</span>');

            return true;
        }
        else
        {

            $('.esig-error-box').remove();
            return false;
        }

    }

    $('body').on('click', '#cc_recipient_emails .deleteIcon', function (e) {

        // checking if signer only one then hide signer order checkbox 
        $(this).parent().remove();

        e.preventDefault();

        $(this).remove();
    });
    $('body').on('click', '#cc_recipient_emails12 .deleteIcon', function (e) {

        // checking if signer only one then hide signer order checkbox 
        $(this).parent().remove();

        e.preventDefault();

        $(this).remove();
    });




})(jQuery);
