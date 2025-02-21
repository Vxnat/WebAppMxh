$(document).ready(function () {
  // Lấy tham số userId từ URL
  const urlParams = new URLSearchParams(window.location.search);
  const userId = urlParams.get('user_id');

  // Hiện edit form khi click edit button
  $(document).on('click', '.edit-btn', function () {
    $('#edit-profile-modal').css('display', 'flex');
    $(document).on('click', '.fa-x', function () {
      $('#edit-profile-modal').hide();
    });
  });

  

  // Đóng edit form khi click ra ngoài
  $(document).on('click', function (event) {
    if (event.target == $('#edit-profile-modal')[0]) {
      $('#edit-profile-modal').hide();
    }
  });
});
