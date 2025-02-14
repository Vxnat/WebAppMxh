$(document).ready(function () {
  // Toggle Noti Container
  $(document).on('click', '#navbar-noti', function () {
    $('.header__notify').toggleClass('active');
    if ($('.header__notify').hasClass('active')) fetchNoti();
  });
  $('.header__notify').on('click', function (e) {
    e.stopPropagation();
  });
  // Hide Noti
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.header__notify, #navbar-noti').length) {
      $('.header__notify').removeClass('active');
    }
  });

  // Accept Friend Request
  $('#notify-list').on('click', '.accept', function () {
    const notificationItem = $(this).closest('.header__notify-item'); // Lấy phần tử thông báo
    const notificationId = notificationItem.data('notification-id'); // ID của thông báo
    const senderId = notificationItem.data('sender-id'); // ID người gửi lời mời
    $.ajax({
      url: '../ajax/home/noti-handler.php',
      type: 'POST',
      data: {
        acceptFriendRequest: true,
        sender_id: senderId,
        notification_id: notificationId,
      },
      success: function (data) {
        if (data.includes('success')) {
          notificationItem.remove();
        } else {
          alert('Có lỗi xảy ra, vui lòng thử lại!');
        }
      },
    });
  });

  // Reject Friend Request
  $('#notify-list').on('click', '.reject', function () {
    const notificationItem = $(this).closest('.header__notify-item'); // Lấy phần tử thông báo
    const notificationId = notificationItem.data('notification-id'); // ID của thông báo
    const senderId = notificationItem.data('sender-id'); // ID người gửi lời mời
    $.ajax({
      url: '../ajax/home/noti-handler.php',
      type: 'POST',
      data: {
        rejectFriendRequest: true,
        sender_id: senderId,
        notification_id: notificationId,
      },
      success: function (data) {
        if (data.includes('success')) {
          notificationItem.remove();
        } else {
          alert('Có lỗi xảy ra, vui lòng thử lại!');
        }
      },
    });
  });

  // Show More Noti
  $('#notify-list').on('click', '#more-noti-btn', function () {
    const lastCreatedAt = $('.header__notify-item').last().data('created-at');

    $.ajax({
      url: '../ajax/home/noti-handler.php',
      method: 'POST',
      data: { moreNoti: true, lastCreatedAt: lastCreatedAt },
      success: function (data) {
        if (data.includes('failed')) {
          $('#more-noti-btn').hide();
          return;
        }
        $('#notify-list').find('.header__notify-list').append(data);
        checkNewNotifications();
      },
    });
  });

  // Get Notifications
  function fetchNoti() {
    $.ajax({
      url: '../ajax/home/noti-handler.php',
      method: 'POST',
      data: { fetchNoti: true },
      success: function (data) {
        $('#notify-list').html(data);
        checkNewNotifications();
      },
    });
  }

  // Check New Noti
  function checkNewNotifications() {
    $.ajax({
      url: '../ajax/home/noti-handler.php', // File xử lý PHP
      method: 'POST',
      data: { checkNewNoti: true },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          const quantity = data.unread_count > 9 ? '9+' : data.unread_count.toString();
          $('.new-noti').html(quantity); // Hiển thị icon thông báo mới
          $('.new-noti').css('display', 'flex');
        } else {
          $('.new-noti').hide(); // Ẩn nếu không có thông báo mới
        }
      },
    });
  }

  checkNewNotifications();
  // Kiểm tra noti mới mỗi 10s
  setInterval(checkNewNotifications, 10000);
});
