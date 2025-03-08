$(document).ready(function () {
  // Chức năng tải danh sách shortcut
  function fetchShortcut() {
    $.ajax({
      url: '../ajax/home/shortcut-handler.php',
      method: 'POST',
      data: { fetchShortcut: true },
      success: function (data) {
        $('#shortcut-list').html(data);
      },
    });
  }

  fetchShortcut();

  // Chức năng xóa shortcut
  $(document).on('click', '.remove-shortcut', function () {
    const shortcutId = $(this).closest('.sidebar__wrapper-item').data('shortcut-id');
    $.ajax({
      url: '../ajax/home/shortcut-handler.php',
      method: 'POST',
      data: { removeShortcut: true, shortcutId: shortcutId },
      success: function (data) {
        if (data) {
          fetchShortcut();
        } else {
          alert('Lỗi xóa shortcut');
        }
      },
    });
  });
});
