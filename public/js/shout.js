var scollBox = $('.shoutbox');
var next_batch = null;
var forceScroll = true;
let messages = $('.chat-messages .list-group');

messages.animate({ scrollTop: messages.prop('scrollHeight') }, 0);

load_data = {
  'fetch': 1
};
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('input[name=_token]').val()
  }
});

messages.scroll(function() {
  forceScroll = false;
  let scrollTop = messages.scrollTop() + messages.prop('clientHeight');
  let scrollHeight = messages.prop('scrollHeight');
  forceScroll = scrollTop >= scrollHeight;
});

function formatTime(seconds) {
    console.log(seconds);
    const MINUTE = 60;
    const HOUR = MINUTE * 60;
    let suffix = "second";
    let value = seconds;
    if (seconds >= HOUR) {
        suffix = "hour";
        value = Math.floor(seconds / HOUR);
    } else if (seconds >= MINUTE) {
        suffix = "minute";
        value = Math.floor(seconds / MINUTE);
    }

    if (value != 1) {
        suffix += "s";
    }

    return value.toString() + " " + suffix;
}

function updateTime() {
    messages.children().each(function (i, message) {
        let createdAt = parseInt(message.getAttribute('data-created'));
        let text = $(".text-muted > small > em", message);
        let deltaTime = Math.floor((new Date).getTime() / 1000) - createdAt;
        text.text(formatTime(deltaTime) + " ago");
    })
}

function updateMessages() {
  $('.chat-messages .list-group');
  $.ajax({
  url: "shoutbox/messages/" + parseInt(next_batch),
  type: 'get',
  data: load_data,
  dataType: 'json',
  success: function(data) {
    if (next_batch === null) {
      next_batch = data.next_batch;
    } else {
      next_batch = data.next_batch;
      data.data.forEach(function(h) {
        let message = $(h);
        messages.append(message);
      });
    }

    if (forceScroll) {
      messages.animate({ scrollTop: messages.prop('scrollHeight') }, 0);
    }
  }});
  updateTime();
  window.setTimeout(updateMessages, 3000);
}

window.setTimeout(updateMessages, 3000);

$("#chat-message").keypress(function(evt) {
  if (evt.which == 13) {
    var message = $('#chat-message').val();
    post_data = {
      'message': message
    };
    $.ajax({
      url: "shoutbox/send",
      type: 'post',
      data: post_data,
      dataType: 'json',
      success: function(data) {
        forceScroll = true;
        $('#chat-error').addClass('hidden');
        $('#chat-message').removeClass('invalid');
        $('#chat-message').val('');
        messages.animate({
          scrollTop: messages.prop('scrollHeight')
        }, 0);
      },
      error: function(data) {
        $('#chat-message').addClass('invalid');
        $('#chat-error').removeClass('hidden');
        $('#chat-error').text('Whoops Im Currently Offline');
      }
    });
  }
});
