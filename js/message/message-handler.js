$(document).ready(function () {
  // start: Sidebar
  // Mở profile để hiện chức năng đăng xuất hoặc logout
  $('.chat-sidebar-profile-toggle').on('click', function (e) {
    e.preventDefault();
    $(this).parent().toggleClass('active');
  });

  // Ẩn profile đi
  $(document).on('click', function (e) {
    if (!$(e.target).is('.chat-sidebar-profile, .chat-sidebar-profile *')) {
      $('.chat-sidebar-profile').removeClass('active');
    }
  });
  // end: Sidebar

  // start: Conversation
  // --> Chức năng nút 3 chấm hiện thao tác ở tin nhắn chat
  $(document).on('click', '.conversation-item-dropdown-toggle', function (e) {
    e.preventDefault();
    if ($(this).parent().hasClass('active')) {
      $(this).parent().removeClass('active');
    } else {
      $('.conversation-item-dropdown').removeClass('active');
      $(this).parent().addClass('active');
    }
  });

  // Ẩn nó đi
  $(document).on('click', function (e) {
    if (!$(e.target).is('.conversation-item-dropdown, .conversation-item-dropdown *')) {
      $('.conversation-item-dropdown').removeClass('active');
    }
  });
  // End: Chức năng hiện thao tác tin nhắn

  // --> Chức năng tăng chiều dài của textbox khi nhấn enter
  $(document).on('input', '.conversation-form-input', function () {
    $(this).attr('rows', $(this).val().split('\n').length);
  });

  // Lấy dữ liệu các cuộc trò chuyện
  function getPersonChatList() {
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: { getPersonChatList: true },
      success: function (response) {
        $('.person-chat').append(response);
      },
    });
  }
  getPersonChatList();

  // Lấy các cuộc trò chuyện nhóm về
  function getGroupChatList() {
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: { getGroupChatList: true },
      success: function (response) {
        $('.group-chat').append(response);
      },
    });
  }

  getGroupChatList();

  // --> Chức năng chuyển qua trang chat khác
  // Chọn tất cả phần từ có thuộc tính data-conversation
  $(document).on('click', '[data-conversation]', function (e) {
    e.preventDefault(); // Ngăn chặn hành vi mặc định của thẻ a


    // Xóa class active cho tất cả các phần tử có class .conversation
    $('.conversation').removeClass('active');

    const conversationId = $(this).data('conversation').replace(/#/g, '');

    $(`#${conversationId}`).addClass('active'); // Thêm class active
    
  });
});
