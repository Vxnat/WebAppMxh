$(document).ready(function () {
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
});
