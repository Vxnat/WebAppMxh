const cloudName = 'dczte5kyo';
const uploadPreset = 'social_media_image';
const baseFolder = 'social_media_uploads'; // Thư mục gốc

// Giới hạn dung lượng
const maxSize = {
  img: 5 * 1024 * 1024, // 5MB
  video: 50 * 1024 * 1024, // 50MB
};

// Tải ảnh lên cloudinary
function uploadToCloudinary(file, type = 'img') {
  return new Promise((resolve, reject) => {
    // Kiểm tra dung lượng file
    if (file.size > maxSize[type]) {
      return reject(`File quá lớn! Giới hạn: ${maxSize[type] / (1024 * 1024)}MB`);
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', uploadPreset);

    const fullFolderPath = type === 'video' ? `${baseFolder}/videos` : `${baseFolder}/images`;

    formData.append('folder', fullFolderPath);
    type == 'video'
      ? (uploadUrl = `https://api.cloudinary.com/v1_1/${cloudName}/video/upload`) // URL upload video
      : (uploadUrl = `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`); // URL upload image

    $.ajax({
      url: uploadUrl,
      type: 'POST',
      data: formData,
      processData: false, // Không xử lý dữ liệu
      contentType: false, // Không đặt loại nội dung
      success: function (response) {
        const mediaUrl = response.secure_url; // URL trả về từ Cloudinary
        resolve(mediaUrl); // Trả về URL từ Cloudinary
      },
      error: function () {
        reject('Error uploading file'); // Nếu lỗi, trả về reject
      },
    });
  });

  // Xóa ảnh trên cloudinary , khi người dùng xóa post hoặc xóa comment
  function deleteToCloudinary() {}
}
