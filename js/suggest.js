$(document).ready(function () {
  // Hàm lây danh sách gợi ý kết bạn
  function getSuggestionsList() {
    $.ajax({
      url: '../ajax/suggest-friend/suggest-friend-handler.php',
      method: 'POST',
      data: { getSuggestionsList: true },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) $('.suggestions-list').html(data.html);
      },
    });
  }
  // Hiển thị danh sách lời mời kết bạn
  function getFriendRequests() {
    $.ajax({
      url: '../ajax/suggest-friend/suggest-friend-handler.php',
      method: 'POST',
      data: { getFriendRequests: true },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) $('.friend-request-list').html(data.html);
        // console.log(data.html);
      },
    });
  }

  getSuggestionsList();
  getFriendRequests();

  // Gửi lời mời kết bạn
  $(document).on('click', '.add', function () {
    const receiverId = $(this).closest('.suggestion').data('user-id');
    $.ajax({
      url: '../ajax/suggest-friend/suggest-friend-handler.php',
      method: 'POST',
      data: { addFriend: true, receiverId: receiverId },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) getSuggestionsList();
      },
    });
  });

  // Chấp nhận Lời mời kết bạn
  $(document).on('click', '.accept', function () {
    const requestId = $(this).closest('.friend-request').data('request-id');
    $.ajax({
      url: '../ajax/suggest-friend/suggest-friend-handler.php',
      method: 'POST',
      data: { acceptFriendRequest: true, requestId: requestId },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert(data.message);
          getFriendRequests();
        }
      },
    });
  });

  // Từ chối Lời mời kết bạn
  $(document).on('click', '.decline', function () {
    const requestId = $(this).closest('.friend-request').data('request-id');
    $.ajax({
      url: '../ajax/suggest-friend/suggest-friend-handler.php',
      method: 'POST',
      data: { declineFriendRequest: true, requestId: requestId },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert(data.message);
          getFriendRequests();
        }
      },
    });
  });

  // Gỡ gợi ý kết bạn
  $(document).on('click', '.delete', function () {
    const suggestion = $(this).closest('.suggestion');
    suggestion.fadeOut(300, function () {
      suggestion.remove();
    });
  });
});
