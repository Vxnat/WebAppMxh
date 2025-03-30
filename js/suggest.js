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
  
    getSuggestionsList();

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

  // Xóa lời mời kết bạn
  $(document).on('click', '.delete', function () {
    const suggestion = $(this).closest('.suggestion');
    suggestion.fadeOut(300, function () {
      suggestion.remove();
    });
  });

});