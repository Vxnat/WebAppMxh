$(document).ready(function () {
  $('#navbar-search').on('keyup click', function () {
    const content = $(this).val().trim();

    if (content === '') {
      $('#search-result').html('').removeClass('active'); // Xóa kết quả và ẩn
      return;
    }

    $.ajax({
      url: '../ajax/home/search-handler.php',
      method: 'POST',
      data: { searchUser: true, content: content },
      success: function (data) {
        $('#search-result').html(data).addClass('active'); // Hiển thị kết quả
      },
    });
  });

  // Ẩn #search-result nếu click ra ngoài ô tìm kiếm hoặc vùng kết quả
  $(document).on('click', function (e) {
    if (!$(e.target).closest('#navbar-search, #search-result').length) {
      $('#search-result').removeClass('active').html('');
    }
  });
});
