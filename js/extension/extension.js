function formatTime(timestamp) {
  // Chuyển đổi chuỗi datetime thành đối tượng Date nếu cần
  const time = new Date(timestamp);

  // Kiểm tra nếu đối tượng Date không hợp lệ
  if (isNaN(time)) {
    return 'Thời gian không hợp lệ';
  }

  const currentTime = new Date();
  const timeDifference = (currentTime - time) / 1000; // tính thời gian chênh lệch tính bằng giây

  // Các giá trị thời gian cơ bản
  const seconds = timeDifference;
  const minutes = Math.floor(seconds / 60);
  const hours = Math.floor(seconds / 3600);
  const days = Math.floor(seconds / 86400);
  const weeks = Math.floor(seconds / 604800);
  const months = Math.floor(seconds / 2592000);

  // Kiểm tra các khoảng thời gian để trả về kết quả phù hợp
  if (seconds < 60) {
    return 'Vài giây trước';
  } else if (minutes < 60) {
    return `${minutes} phút trước`;
  } else if (hours < 24) {
    return `${hours} giờ trước`;
  } else if (days === 1) {
    return 'Hôm qua';
  } else if (days < 7) {
    return `${days} ngày trước`;
  } else if (weeks < 4) {
    return `${weeks} tuần trước`;
  } else if (months < 12) {
    return `${months} tháng trước`;
  } else {
    // Hiển thị ngày/tháng/năm nếu thời gian lâu hơn 1 năm
    const day = time.getDate().toString().padStart(2, '0');
    const month = (time.getMonth() + 1).toString().padStart(2, '0');
    const year = time.getFullYear();
    return `${day}/${month}/${year}`;
  }
}
