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

});