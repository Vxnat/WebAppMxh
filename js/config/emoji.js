$(document).ready(function () {
  let emojiData = null; // Lưu dữ liệu emoji để tránh gọi lại API
  let isFetching = false; // Tránh gọi API nhiều lần khi đang tải -> Kiểm tra xem API đã

  // Hiển thị Emoji List
  $(document).on('click', '.emoji-icon', function () {
    let emojiSelector = $(this).find('.emoji-selector');

    // Nếu có emoji selector khác đang mở, ẩn nó đi
    $('.emoji-selector.active').not(emojiSelector).removeClass('active');

    // Toggle class active
    emojiSelector.toggleClass('active');

    // Chỉ gọi API nếu .emoji-selector vừa được bật (active) và chưa có dữ liệu
    if (emojiSelector.hasClass('active') && !emojiData && !isFetching) {
      isFetching = true; // Đánh dấu đang tải API
      emojiSelector.find('.emoji-loading').show(); // Hiển thị loading

      $.getJSON('https://emoji-api.com/emojis?access_key=62bbf9095bccde0df64c81bf72ad2d944fbdd0aa', function (data) {
        isFetching = false; // Xóa trạng thái tải
        emojiSelector.find('.emoji-loading').hide(); // Ẩn loading

        if (!Array.isArray(data) || data.length === 0) {
          console.error('Lỗi: API không trả về danh sách emoji hợp lệ.');
          return;
        }

        emojiData = data; // Lưu dữ liệu để tránh gọi API lần nữa
        loadEmoji(emojiSelector, emojiData);
      }).fail(function () {
        isFetching = false; // Xóa trạng thái tải nếu lỗi xảy ra
        console.error('Lỗi: Không thể lấy dữ liệu từ API.');
      });
    } else if (emojiData) {
      // Nếu dữ liệu đã có, chỉ cần hiển thị lại danh sách
      loadEmoji(emojiSelector, emojiData);
    }
  });

  // Khi click ra ngoài .emoji-selector, ẩn nó đi
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.emoji-selector, .emoji-icon').length) {
      // Nếu click ra ngoài .emoji-selector và .emoji-icon, ẩn emoji-selector
      $('.emoji-selector').removeClass('active');
    }
  });

  // Search Emoji
  $(document).on('keyup', '.emoji-selector .input-container input', function () {
    const val = $(this).val().toLowerCase(); // Chuyển đổi giá trị tìm kiếm thành chữ thường
    let $emojiListItems = $('#emojiList li'); // Lấy tất cả các emoji trong danh sách

    $emojiListItems.each(function () {
      let emojiName = $(this).attr('emoji-name'); // Lấy giá trị attribute 'emoji-name' của từng emoji
      if (emojiName && emojiName.toLowerCase().includes(val)) {
        $(this).css('display', 'flex'); // Hiển thị nếu tên emoji chứa giá trị tìm kiếm
      } else {
        $(this).css('display', 'none'); // Ẩn nếu không có giá trị tìm kiếm
      }
    });
  });

  // Click Emoji
  $(document).on('click', '#emojiList li', function () {
    let emoji = $(this).text(); // Lấy emoji từ nội dung của li

    // Tìm phần tử cha của .emoji-icon rồi tìm đến textarea trong phần tử cha đó
    let content = $(this).closest('.emoji-icon').parent().find('textarea');

    // Thêm emoji vào textarea
    content.val(content.val() + emoji); // Chèn emoji vào cuối nội dung của textarea
  });

  // Ngăn chặn tắt khi nhấn vào Emoji
  $(document).on('click', '.emoji-selector', function (e) {
    e.stopPropagation();
  });

  // Load Emoji
  function loadEmoji(self, data) {
    let $emojiList = $(self).find('#emojiList');
    if ($emojiList.length === 0) {
      console.error('Lỗi: Không tìm thấy phần tử #emojiList');
      return;
    }

    // Xóa emoji cũ trước khi thêm mới (tránh trùng lặp)
    $emojiList.empty();

    $.each(data, function (index, emoji) {
      // Loại bỏ các emoji bị lỗi
      if (
        !emoji.character ||
        emoji.slug.startsWith('e13') ||
        emoji.slug.startsWith('e14') ||
        emoji.slug.startsWith('e15')
      ) {
        return;
      }

      // Kiểm tra nếu emoji chứa nhiều ký tự Unicode
      let emojiLength = Array.from(emoji.character).length;
      if (emojiLength > 1) {
        return;
      }

      let $li = $('<li></li>')
        .attr('emoji-name', emoji.slug || 'unknown')
        .text(emoji.character);

      $emojiList.append($li);
    });

    console.log('Danh sách emoji đã tải xong.');
  }
});
