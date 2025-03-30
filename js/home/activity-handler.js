$(document).ready(function () {
  // Get Activity
  function fetchActivity() {
    $.ajax({
      url: '../ajax/home/activity-handler.php',
      method: 'POST',
      data: { fetchActivity: true },
      success: function (data) {
        $('#activity-list').html(data);
      },
    });
  }

  fetchActivity();
});
