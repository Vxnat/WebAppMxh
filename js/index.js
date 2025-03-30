$(document).ready(function () {
  $('#register').on('click', function () {
    $('#container').addClass('active');
  });

  $('#login').on('click', function () {
    $('#container').removeClass('active');
  });

  // Click Enter để Login hoặc Register
  function handleClickEnter() {
    $(document).on('keyup', function (event) {
      if (event.which === 13 && !event.shiftKey) {
        event.preventDefault(); // Ngăn nhập dòng mới

        // Kiểm tra container có active không?
        if ($('#container').hasClass('active')) {
          $('#su-btn').click(); // Đăng ký
        } else {
          $('#si-btn').click(); // Đăng nhập
        }
      }
    });
  }

  handleClickEnter();

  // Login
  $('#si-btn').on('click', function () {
    var email = $('#si-email').val();
    var password = $('#si-pw').val();

    if (email === '' || password === '') {
      alert('Vui lòng nhập đầy đủ thông tin!');
      return;
    }

    $.ajax({
      url: '../ajax/home/auth-handler.php',
      method: 'POST',
      data: { signIn: true, email: email, password: password },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          window.location.replace(data.redirect);
        } else {
          alert(data.message);
        }
      },
    });
  });

  // Sign up
  $('#su-btn').on('click', function () {
    var email = $('#su-email').val();
    var password = $('#su-pw').val();
    var cfPassword = $('#cf-pw').val();

    if (email === '' || password === '' || cfPassword === '') {
      alert('Vui lòng nhập đầy đủ thông tin!');
      return;
    }

    if (password !== cfPassword) {
      alert('Mat khau ko giong nhau');
      return;
    }

    $.ajax({
      url: '../ajax/home/auth-handler.php',
      method: 'POST',
      data: { signUp: true, email: email, password: password },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          window.location.replace(data.redirect);
        } else {
          alert(data.message);
        }
      },
    });
  });

  // Show Form Logout
  $('#navbar-logout').on('click', function () {
    const dialogForm = $('.dialog-container');
    const html = `
      <div class="dialog-wrapper">
        <div class="dialog-header">
          Logout?
        </div>
        <div class="dialog-content">Are you sure ?</div>
        <div class="dialog-actions">
          <button class="cancel-button">Cancel</button>
          <button id="logout-btn" style="background-color:red;">Logout</button>
        </div>
      </div>
    `;
    dialogForm.html(html);
    dialogForm.addClass('active');
  });

  // Logout
  $(document).on('click', '#logout-btn', function () {
    $.ajax({
      url: '../ajax/home/auth-handler.php',
      method: 'POST',
      data: { logout: true },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          window.location.replace(data.redirect);
        } else {
          alert(data.message);
        }
      },
    });
  });

  // Send Email Reset PW
  $('#send-email-reset-btn').on('click', function () {
    var email = $('#reset-email').val();
    // Vô hiệu hóa nút gửi email reset
    $('#send-email-reset-btn').addClass('disable');
    $.ajax({
      url: '../ajax/send_email.php',
      method: 'POST',
      data: { sendEmailResetPw: true, email: email },
      success: function (data) {
        // Can phai them disable khi da nhan nut
        alert(data);
      },
      complete: function () {
        $('#send-email-reset-btn').removeClass('disable');
      },
    });
  });

  // Update Pw
  $('#update-pw-btn').on('click', function () {
    var pw = $('#reset-pw').val();
    var cfpw = $('#cf-reset-pw').val();
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (pw !== cfpw) {
      alert('Mat khau ko trung khop!');
    } else {
      $.ajax({
        url: '../ajax/home/auth-handler.php',
        method: 'POST',
        data: { updatePw: true, pw: pw, token: token },
        success: function (data) {
          alert(data);
        },
      });
    }
  });
});
