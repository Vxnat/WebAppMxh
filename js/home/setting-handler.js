$(document).ready(function () {
  // Toggle SideBar
  $('.sidebar')
    .on('mouseenter', function () {
      $(this).removeClass('close');
    })
    .on('mouseleave', function () {
      $(this).addClass('close');
    });

  // Arrow Dropdown Menu
  $('.arrow').on('click', function () {
    $(this).closest('li').toggleClass('show');
  });

  // Toggle Password
  $('.toggle-password').on('click', function () {
    const targetId = $(this).data('target');
    const inputField = $(`#${targetId}`);

    if (inputField.attr('type') === 'password') {
      inputField.attr('type', 'text');
      $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      inputField.attr('type', 'password');
      $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  // Toggle giua privacy va thay doi mat khau
  $('#privacy').on('click', function () {
    $('#privacy-section').show();
    $('#change-password-form').hide();
  });

  $('#password').on('click', function () {
    $('#privacy-section').hide();
    $('#change-password-form').show();
  });

  // Lay du lieu Privacy ve
  function getPrivacySections() {
    $.ajax({
      url: '../ajax/home/setting-handler.php',
      method: 'POST',
      data: { getPrivacySections: true },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          const dataSetting = data.privacySections;
          console.log(dataSetting.show_email);

          const html = `
          <div class="form-group">
            <label for="show-email">Show Email:</label>
            <label class="switch">
              <input type="checkbox" ${dataSetting.show_email ? 'checked' : ''} id="show-email" />
              <span class="slider round"></span>
            </label>
          </div>
          <div class="form-group">
            <label for="show-avatar">Show Avatar:</label>
            <label class="switch">
              <input type="checkbox" ${dataSetting.show_avatar ? 'checked' : ''} id="show-avatar" />
              <span class="slider round"></span>
            </label>
          </div>`;

          $('#privacy-section').html(html);
        } else {
          alert(data.error);
        }
      },
    });
  }
  getPrivacySections();

  // Ham cap nhat privacy setting
  function updatePrivacySetting(settingKey, settingValue) {
    let data = { updatePrivacySetting: true };
    data[settingKey] = settingValue;

    $.ajax({
      url: '../ajax/home/setting-handler.php',
      method: 'POST',
      data: data,
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
        } else {
          alert(data.error);
        }
      },
      error: function () {
        alert('Có lỗi xảy ra, vui lòng thử lại.');
      },
    });
  }

  // Khi người dùng thay đổi privacy
  $(document).on('click', '#show-email', function () {
    updatePrivacySetting('showEmail', $(this).is(':checked') ? 1 : 0);
  });

  $(document).on('click', '#show-avatar', function () {
    updatePrivacySetting('showAvatar', $(this).is(':checked') ? 1 : 0);
  });

  // Change Password
  $('#change-password-btn').on('click', function (e) {
    e.preventDefault();
    const oldPassword = $('#current-password').val().trim();
    const newPassword = $('#new-password').val().trim();
    const confirmPassword = $('#confirm-password').val().trim();

    if (newPassword !== confirmPassword) {
      alert('Mật khẩu mới không trùng khớp với nhau.');
      return;
    }
    $.ajax({
      url: '../ajax/home/setting-handler.php',
      method: 'POST',
      data: { changePassword: true, oldPassword: oldPassword, newPassword: newPassword },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert(data.message);
          window.location.reload();
        } else {
          alert(data.error);
        }
      },
      error: function () {
        alert('Có lỗi xảy ra, vui lòng thử lại.');
      },
    });
  });
});
