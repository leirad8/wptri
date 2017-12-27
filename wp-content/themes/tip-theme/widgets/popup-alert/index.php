<?php

 ?>

<div id="popup-alert-wrapper" class="widget">
  <div id="popup-alert">
    <!-- <div id="popup-x-close" class="popup-close">( X )</div> -->
    <h2 id="popup-alert-title">popup alert</h2>
    <div id="popup-alert-msg"></div>

    <div class="button-wrapper small-green" style="text-align: center;">
      <button onClick="closePopupAlert()">Close</button>
    </div>
  </div>
</div>

<script type="text/javascript">
  // .mc4wp-response .mc4wp-alert
  var mailchimpItems = document.getElementsByClassName('mc4wp-response');
  var mailchimp = (mailchimpItems.length >= 1) ? mailchimpItems[0] : null
  var mailchimpAlertItems = document.getElementsByClassName('mc4wp-alert');
  var mailchimpAlert = (mailchimpAlertItems.length >= 1) ? mailchimpAlertItems[0] : null

  var popupWrapper = document.getElementById('popup-alert-wrapper');
  var popupTitle = document.getElementById('popup-alert-title');
  var popupMsg = document.getElementById('popup-alert-msg');

  var email = document.getElementById("mc-input-email");
  var mcSubmit = document.getElementById('mc-submit')

  var validEmail = false;


  if(email && mcSubmit) {

    email.addEventListener("input", function (event) {
      if (email.validity.typeMismatch) {
        email.setCustomValidity("I expect an e-mail!");
        validEmail = false;
        return
      } else {
        email.setCustomValidity("");
        validEmail = true;
      }
    });

    // animate the submit btn when clicked/processing
    if (mcSubmit) {
      mcSubmit.addEventListener('click', function(e) {
        if (!validEmail) return
        this.style.backgroundColor = "rgba(0, 0, 0, .2)";
        //this.disabled = true;
        this.value = "Please wait..."
      });
    }

    document.getElementById('popup-alert-wrapper').addEventListener('click', function(e) {
      closePopupAlert();
    });

    if (mailchimp.innerHTML != '') {
      // check if mailchimp has msg content, if so, copy it to the global alert and display
      popupTitle.innerHTML = 'TIP Updates Newsletter';
      popupMsg.innerHTML = mailchimpAlert.innerHTML
      mailchimp.style.display = 'none';
      popupWrapper.style.display = 'flex';
    } else {
      popupWrapper.style.display = 'none';
    }

  } else {
    popupWrapper.style.display = 'none';
  }

  function closePopupAlert() {
    var popupWrapper = document.getElementById('popup-alert-wrapper');
    popupWrapper.style.display = 'none';
  }


</script>
