$(document).ready(function() {

  grecaptcha.ready(function () {    
      grecaptcha.execute(recaptchakey, { action: 'contact' }).then(function (token) {
          var recaptchaResponse = document.getElementById('recaptchaResponse');
          recaptchaResponse.value = token;
          // Make the Ajax call here
      });
  });

});