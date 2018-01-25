// Auto
  $(function() {

    // Check for on keypress
    $("input").on("keydown", function(event) {

      var self = $(this);

      // Keyboard Controls
      var controls = [8, 16, 18, 17, 20, 35, 36, 37, 38, 39, 40, 45, 46, 9, 91, 93, 224, 13, 127, 27, 32];

      // Special Chars
      var specialChars = [43, 61, 186, 187, 188, 189, 190, 191, 192, 219, 220, 221, 222];

      // Numbers
      var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];

      var preCombined = controls.concat(numbers);
      var combined = preCombined;

      // Allow Letter
      for (var i = 65; i <= 90; i++) {
        combined.push(i);
      }

      // handle Input
      if ($.inArray(event.which, combined) === -1) {
        event.preventDefault();
      }

      // Handle Autostepper
      if ($.inArray(event.which, controls.concat(specialChars)) === -1) {
        setTimeout(function() {
          if (self.hasClass('last-input')) {
            $('#submit_verification').focus();
          } else {
            self.parent().parent().next().find('input').focus();
          }
        }, 1);
      }

    });
    // Check for cop and paste
    $("input").on("input", function() {
      var regexp = /[^a-zA-Z0-9]/g;
      if ($(this).val().match(regexp)) {
        $(this).val($(this).val().replace(regexp, ''));
      }
    });

  });

// Verify
  $.ajaxSetup({
    beforeSend: function(xhr, type) {
      if (!type.crossDomain) {
        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
      }
    },
  });

$('.code-inputs').on('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
  $('.code-inputs').delay(200).removeClass('invalid-shake');
});

$("#submit_verification").click(function(event) {
  event.preventDefault();

  var formData = $('#verification_form').serialize();

  $.ajax({
    url: "{{ route('verify') }}",
    type: "post",
    dataType: 'json',
    data: formData,
    success: function(response, status, data) {
      window.location.href = data.responseJSON.nextUri;
    },
    error: function(response, status, error) {
      if (response.status === 418) {

        var remainingAttempts = response.responseJSON.remainingAttempts;
        var submitTrigger = $('#submit_verification');
        var varificationForm = $('#verification_form');

        $('.code-inputs').addClass('invalid-shake');
        varificationForm[0].reset();
        $('#remaining_attempts').text(remainingAttempts);

        switch (remainingAttempts) {
          case 0:
            submitTrigger.addClass('btn-danger');
            swal(
              "{{ trans('auth.verificationLockedTitle') }}",
              "{{ trans('auth.verificationLockedMessage') }}",
              'error'
            );
            break;

          case 1:
            submitTrigger.addClass('btn-danger');
            swal(
              "{{ trans('auth.verificationWarningTitle') }}",
              "{{ trans('auth.verificationWarningMessage', ['hours' => $hoursToExpire, 'minutes' => $minutesToExpire,]) }}",
              'error'
            );
            break;

          case 2:
            submitTrigger.addClass('btn-warning');
            break;

          case 3:
            submitTrigger.addClass('btn-info');
            break;

          default:
            submitTrigger.addClass('btn-success');
            break;
        }

        if (remainingAttempts === 0) {
          $('#verification_status_title').html('<h3>{{ trans('
            auth.titleFailed ') }}</h3>');
          varificationForm.fadeOut(100, function() {

            $('#failed_login_alert').show();

            setTimeout(function() {
              $('body').fadeOut(100, function() {
                location.reload();
              });
            }, 2000);
          });
        };

      };
    }
  });
});

// Resend
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

$("#resend_code_trigger").click(function(event) {
  event.preventDefault();

  var self = $(this);
  var resultStatus;
  var resultData;
  var endpoint = "{{ route('resend') }}";

  self.addClass('disabled')
    .attr("disabled", true);

  swal({
    text: 'Sending verification code ...',
    allowOutsideClick: false,
    grow: false,
    animation: false,
    onOpen: () => {
      swal.showLoading();
      $.ajax({
        type: "POST",
        url: endpoint,
        success: function(response, status, data) {
          swalCallback(response.title, response.message, status);
        },
        error: function(response, status, error) {
          swalCallback(error, error, status);
        }
      })
    }
  });

  function swalCallback(title, message, status) {
    swal({
      text: title,
      text: message,
      type: status,
      grow: false,
      animation: false,
      allowOutsideClick: false,
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-lg btn-' + status,
      confirmButtonText: "{{ trans('auth.verificationModalConfBtn') }}",
    });
    self.removeClass('disabled').attr("disabled", false);
  }
});
