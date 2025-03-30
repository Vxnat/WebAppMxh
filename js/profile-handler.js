$(document).ready(function () {
  // Lấy tham số profileUserId từ URL
  const urlParams = new URLSearchParams(window.location.search);
  const profileUserId = urlParams.get('user_id');

  // Hàm lấy dữ liệu người dùng
  function getInfoUser() {
    $.ajax({
      url: '../ajax/profile/profile-handler.php',
      method: 'POST',
      data: {
        get_user_profile: true,
        profileUserId: profileUserId,
      },
      success: function (response) {
        $('.profile-container').html(response);
      },
    });
  }

  getInfoUser();

  // Hiện edit form khi click edit button
  $(document).on('click', '.edit-btn', function () {
    $.ajax({
      url: '../ajax/profile/profile-handler.php',
      method: 'POST',
      data: {
        get_edit_profile_content: true,
      },
      success: function (response) {
        $('#edit-profile-modal').html(response).css('display', 'flex');
      },
      complete: function () {
        $(document).on('click', '.fa-x', function () {
          $('#edit-profile-modal').hide();
        });
      },
    });
  });

  // Xác nhận thay đổi thông tin người dùng
  $(document).on('click', '#save-profile-btn', function () {
    // Lấy dữ liệu từ các input
    const name = $('.edit_introduction_c input:eq(0)').val().trim();
    const birthday = $('.edit_introduction_c input:eq(1)').val().trim();
    const location = $('.edit_introduction_c input:eq(2)').val().trim();
    const bio = $('#bio-input').val().trim();

    // Kiểm tra dữ liệu hợp lệ
    if (name === '') {
      alert('Tên người dùng không được để trống !');
      return;
    }

    $.ajax({
      url: '../ajax/profile/profile-handler.php',
      method: 'POST',
      data: {
        save_edit_profile: true,
        name: name,
        birthday: birthday,
        location: location,
        bio: bio,
      },
      success: function (response) {
        if (response.trim() === 'success') {
          alert('Cập nhật thông tin thành công!');
          window.location.reload(); // Tải lại trang để cập nhật giao diện
        } else {
          alert('Lỗi: ' + response);
        }
      },
      error: function () {
        alert('Lỗi , vui lòng thử lại sau!');
      },
    });
  });

  // Đóng edit form khi click ra ngoài
  $(document).on('click', function (event) {
    if (event.target == $('#edit-profile-modal')[0]) {
      $('#edit-profile-modal').hide();
    }
  });

  // Chức năng thay đổi ảnh bìa
  $(document).on('change', '#cover-photo-upload', async function (e) {
    const file = e.target.files[0];
    const backgroundImg = $('.background-img');

    let result = confirm('Are you sure you want to change the cover photo?');
    if (result) {
      try {
        $('.cover-btn').css('pointer-events', 'none');
        const newImg = $('#cover-photo-upload')[0].files[0];
        backgroundImg.addClass('upload');
        const backgroundUrl = await uploadToCloudinary(newImg);
        $.ajax({
          url: '../ajax/profile/profile-handler.php',
          method: 'POST',
          data: {
            change_cover_photo: true,
            profileUserId: profileUserId,
            background_url: backgroundUrl,
          },
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              backgroundImg.removeClass('upload');
              const reader = new FileReader();
              reader.onload = function (event) {
                backgroundImg.attr('src', event.target.result);
              };
              reader.readAsDataURL(file);
            } else {
              alert('Change photo failed!');
            }
          },
        });
      } catch (e) {
        console.log(e);
      }
    } else {
      backgroundImg.removeClass('upload');
    }

    $('.cover-btn').css('pointer-events', 'auto');
  });

  // Chức năng thay đổi avatar
  $(document).on('change', '#avatar-upload', async function (e) {
    const file = e.target.files[0];
    const avatarImg = $('.avatar');

    let result = confirm('Are you sure you want to change avatar ?');
    if (result) {
      try {
        $('.avatar-btn').css('pointer-events', 'none');
        const newImg = $('#avatar-upload')[0].files[0];
        avatarImg.addClass('upload');
        const avatarUrl = await uploadToCloudinary(newImg);
        $.ajax({
          url: '../ajax/profile/profile-handler.php',
          method: 'POST',
          data: {
            change_avatar: true,
            profileUserId: profileUserId,
            avatar_url: avatarUrl,
          },
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              avatarImg.removeClass('upload');
              const reader = new FileReader();
              reader.onload = function (event) {
                avatarImg.attr('src', event.target.result);
              };
              reader.readAsDataURL(file);
            } else {
              alert('Change photo failed!');
            }
          },
        });
      } catch (e) {
        console.log(e);
      }
    } else {
      avatarImg.removeClass('upload');
    }

    $('.avatar-btn').css('pointer-events', 'auto');
  });

  // Chức năng hiển thị danh sách tất cả bạn bè
  $(document).on('click', '#all-friend-btn', function (e) {
    e.preventDefault();
    $.ajax({
      url: '../ajax/profile/profile-handler.php',
      method: 'POST',
      data: {
        get_all_friends: true,
        profileUserId: profileUserId,
      },
      success: function (response) {
        const html = `<div class='dialog-wrapper'>
          <div class='dialog-header'>
          <p>Danh sách bạn bè</p>
          </div>
          ${response}
        </div>`;
        $('.dialog-container').addClass('active').html(html);
      },
    });
  });

  
 

  

  

  

  

  

  // Đóng dialog
  $(document).on('click', function (event) {
    if (!$(event.target).closest('.dialog-wrapper').length && $('.dialog-container').hasClass('active')) {
      $('.dialog-container').removeClass('active').empty();
    }
  });
});
