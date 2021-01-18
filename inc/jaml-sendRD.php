<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'wp_footer', 'jaml_sendrd_script');

function jaml_sendrd_script() { ?>
<script type="text/javascript">
(function ($) {
  $.fn.JAMLSendRD = function (options) {
    var settings = $.extend(
      {
        textRDMessage: "#textRDMessage",
        borderRDDefault: "#ccc",
        backgroundRDDefault: "#FFF",
        colorRDError: "#dc3545",
        backgroundRDErrorColor: "#fbeaec",
        messageRDSuccess: "Inscrição realizada com sucesso",
      },
      options
    );

    var formRD = this;

    var inputsRD = formRD.find('.jaml-form-control');

    var textRdMessage = formRD.find(settings.textRDMessage)

    var inputRDError = {
      border: "1px solid " + settings.colorRDError,
      background: settings.backgroundRDErrorColor,
    };

    var inputRDDefault = {
      border: "1px solid " + settings.borderRDDefault,
      background: settings.backgroundRDDefault,
    };

    inputsRD.css(inputRDDefault);

    textRdMessage.html("&nbsp");

    inputsRD.parent("div").find(".jaml-form-phone").mask("00 000000000");
    inputsRD.parent("div").find(".jaml-form-cpf").mask("00000000000", { reverse: true });

    function checkRDEmail(inputEmail) {
      if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(inputEmail)) {
        return true;
      }
      return false;
    }

    function isRDValidCPF(cpf) {
      if (typeof cpf !== "string") return false;
      cpf = cpf.replace(/[\s.-]*/gim, "");
      if (
        !cpf ||
        cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999"
      ) {
        return false;
      }
      var soma = 0;
      var resto;
      for (var i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
      resto = (soma * 10) % 11;
      if (resto == 10 || resto == 11) resto = 0;
      if (resto != parseInt(cpf.substring(9, 10))) return false;
      soma = 0;
      for (var i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
      resto = (soma * 10) % 11;
      if (resto == 10 || resto == 11) resto = 0;
      if (resto != parseInt(cpf.substring(10, 11))) return false;
      return true;
    }

    function validateRDForm(input) {
      inputsRD.css(inputRDDefault);
      textRdMessage.html("&nbsp");
      for (var i = 0; i < input.length; i++) {
        if (input.eq(i).hasClass("jaml-form-text") && input.eq(i).hasClass("jaml-form-valid") && input.eq(i).val() === "") {
          input.eq(i).css(inputRDError);
          textRdMessage.html(input.eq(i).siblings("small").text()).css("color", settings.colorRDError);
          return false;
        }

        if (input.eq(i).hasClass("jaml-form-phone") && input.eq(i).hasClass("jaml-form-valid") && input.eq(i).val() === "") {
          input.eq(i).css(inputRDError);
          textRdMessage.html(input.eq(i).siblings("small").text()).css("color", settings.colorRDError);
          return false;
        }

        if (input.eq(i).hasClass("jaml-form-email") && input.eq(i).hasClass("jaml-form-valid")) {
          if (!checkRDEmail(input.eq(i).val())) {
            input.eq(i).css(inputRDError);
            textRdMessage.html(input.eq(i).siblings("small").text()).css("color", settings.colorRDError);
            return false;
          }
        }

        if (input.eq(i).hasClass("jaml-form-cpf") && input.eq(i).hasClass("jaml-form-valid")) {
          if (!isEmailValidCPF(input.eq(i).val())) {
            input.eq(i).css(inputRDError);
            textRdMessage.html(input.eq(i).siblings("small").text()).css("color", settings.colorRDError);
            return false;
          }
        }
      }
      return true;
    }

    formRD.submit(function (e) {
      e.preventDefault();
      var actionurl = e.currentTarget.action;

      if (validateRDForm(inputsRD)) {
        $.ajax({
          url: actionurl,
          type: "post",
          dataType: "application/json",
          data: $(".fmc-modal").serialize(),
          beforeSend: function (data) {
            textRdMessage.html("Enviando...");
          },
          complete: function (data) {
            textRdMessage.html('<span style="color:#198754"> ' + settings.messageRDSuccess + "</span>");
            inputsRD.val("");
          },
        });
      }
    });
  };
})(jQuery);
</script>    

<?php }