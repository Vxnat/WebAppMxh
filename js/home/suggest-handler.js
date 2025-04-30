$(document).ready(function () {
  $(document).on('click', '.toggle-add-friend', function () {
    const self = $(this);
    const receiverId = self.closest('.sidebar__wrapper-item').data('user-id');

    if (self.hasClass('sent-request')) {
      // Nếu đã gửi, hủy lời mời
      $.ajax({
        url: '../ajax/home/suggest-handler.php',
        method: 'POST',
        data: { cancelFriendRequest: true, receiverId: receiverId },
        success: function (response) {
          if (response.includes('success')) {
            self.removeClass('sent-request').html('<i style="color:#1877f2" class="fa-solid fa-user-plus"></i>');
          } else {
            alert(response);
          }
        },
      });
    } else {
      // Nếu chưa gửi, gửi lời mời
      $.ajax({
        url: '../ajax/home/suggest-handler.php',
        method: 'POST',
        data: { sendFriendRequest: true, receiverId: receiverId },
        success: function (response) {
          if (response.includes('success')) {
            self.addClass('sent-request').html('<i style="color:red;" class="fa-solid fa-user-minus"></i>');
          } else {
            alert(response);
          }
        },
      });
    }
  });

  function fetchSuggestFriends() {
    $.ajax({
      url: '../ajax/home/suggest-handler.php',
      method: 'POST',
      data: { fetchSuggest: true },
      success: function (data) {
        $('#suggest-list').html(data);
      },
    });
  }

  fetchSuggestFriends();
});
