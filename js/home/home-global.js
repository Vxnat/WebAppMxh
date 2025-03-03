$(document).ready(function () {
  // Close dialog
  $(document).on('click', '#close-dialog', function () {
    $('.dialog-container').removeClass('active').empty();
  });

  // Close Dialog
  $(document).on('click', '.cancel-button', function () {
    const dialogForm = $('.dialog-container');
    dialogForm.removeClass('active').empty();
  });

  // Open List Action Header
  $('.header__info').on('click', '.header__infor-avatar', function () {
    $('.header__info').find('.wrapper').toggleClass('active');
  });

  // Hide List Action Header when clicking outside
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.header__info').length) {
      $('.header__info').find('.wrapper').removeClass('active');
    }
  });

  // Get Info User
  function fetchInfoUser() {
    $.ajax({
      url: '../ajax/ajax_action.php',
      method: 'POST',
      data: { fetchInfoUser: true },
      success: function (data) {
        $('#info-user').html(data);
      },
    });
  }

  fetchInfoUser();
});

// Tự động phát video khi video nằm ở nửa khung hình máy tính
document.addEventListener('DOMContentLoaded', function () {
  let playingVideo = null;

  // Hàm xử lý phát/dừng video
  function handleVideos() {
    const videos = document.querySelectorAll('.post__list video');

    if (videos.length === 0) {
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const video = entry.target;

          if (entry.isIntersecting) {
            if (playingVideo && playingVideo !== video) {
              playingVideo.pause();
              playingVideo.currentTime = 0;
            }
            video.play();
            playingVideo = video;
          } else {
            video.pause();
          }
        });
      },
      { threshold: 0.5 },
    );

    videos.forEach((video) => observer.observe(video));
  }

  // Tạo observer để theo dõi khi backend load video xong
  const mutationObserver = new MutationObserver((mutationsList) => {
    mutationsList.forEach((mutation) => {
      if (mutation.type === 'childList') {
        handleVideos(); // Gọi lại khi có thay đổi trong .post__list
      }
    });
  });

  // Bắt đầu quan sát .post__list để phát hiện khi video được thêm vào
  const postList = document.querySelector('.post__list');
  if (postList) {
    mutationObserver.observe(postList, { childList: true, subtree: true });
  }
});
