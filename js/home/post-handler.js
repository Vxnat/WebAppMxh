$(document).ready(function () {
  $('.create__post-header input').on('click', toggleCreatePostContainer);
  $('.create__post-action_item').on('click', toggleCreatePostContainer);
  $(document).on('change', '#create-post-video', handleMediaUpload('video'));
  $(document).on('change', '#create-post-img', handleMediaUpload('img'));

  // Create Post
  $(document).on('click', '#create-post-btn', async function () {
    const content = $('.text-area').val();
    const imgInput = $('#create-post-img')[0]; // Lấy phần tử input file cho ảnh
    const videoInput = $('#create-post-video')[0]; // Lấy phần tử input file cho video

    let file = null; // Biến lưu trữ file
    let type = ''; // (phụ thuộc vào loại file)

    // Kiểm tra input nào có file
    if (imgInput.files.length > 0) {
      file = imgInput.files[0];
      type = 'img';
    } else if (videoInput.files.length > 0) {
      file = videoInput.files[0];
      type = 'video';
    }

    // Nếu không có media , chỉ gửi content lên backend
    if (!file) {
      $.ajax({
        url: '../ajax/home/post-handler.php',
        type: 'POST',
        data: {
          createPost: true,
          content: content.trim(),
          media_url: '', // Không có media_url nếu không có file
        },
        success: function (data) {
          if (data.includes('true')) {
            fetchPost();
          }
        },
        complete: function () {
          clearCreatePost();
        },
      });
      return; // Dừng lại, không cần tiếp tục upload
    }

    // Tải tệp lên Cloudinary và lấy URL ảnh gửi sang BE
    try {
      // Hiện xoay tròn khi tải lên csdl
      $('.overlay').addClass('active');
      const mediaUrl = await uploadToCloudinary(file, type); // Đợi lấy URL từ Cloudinary
      $.ajax({
        url: '../ajax/home/post-handler.php',
        type: 'POST',
        data: {
          createPost: true,
          content: content,
          media_url: mediaUrl,
        },
        success: function (data) {
          if (data.includes('true')) {
            fetchPost();
          }
        },
        complete: function () {
          clearCreatePost();
        },
      });
    } catch (error) {
      alert('Error uploading media.');
    }
  });

  // Dùng để chọn 1 media cho post
  function handleMediaUpload(type) {
    return function () {
      const file = this.files[0];
      if (file) {
        const itemUrl = URL.createObjectURL(file);
        const mediaItem = $('<li>').addClass('create__post-media_item');
        const itemContainer = $('<div>').addClass('media_item');
        let e = type === 'img' ? $('<img>') : $('<video>');
        e.attr('src', itemUrl);
        const closeButton = $('<div>').html('&times;');
        closeButton.addClass('remove-media');

        closeButton.on('click', function () {
          mediaItem.remove(); // Xóa phương tiện khỏi danh sách
        });

        // Xóa danh sách cũ và thêm phương tiện mới
        $('.create__post-media_list').html('');
        mediaItem.append(itemContainer.append(e));
        itemContainer.append(closeButton);
        $('.create__post-media_list').append(mediaItem);
      }
    };
  }

  // Dùng để chọn nhiều media cho post
  function handleMediasUpload(type) {
    return function () {
      const files = this.files;
      if (!files.length) return;

      $.each(files, function (_, file) {
        const itemUrl = URL.createObjectURL(file);
        const mediaItem = $('<li>').addClass('create__post-media_item');
        const itemContainer = $('<div>').addClass('media_item');
        const mediaElement = type === 'img' ? $('<img>') : $('<video controls>');

        mediaElement.attr('src', itemUrl);

        const closeButton = $('<p>').html('<i class="far fa-times-circle"></i>').addClass('remove-media');
        closeButton.on('click', function () {
          mediaItem.remove();
        });

        mediaItem.append(itemContainer.append(mediaElement)).append(closeButton);
        $('.create__post-media_list').append(mediaItem);
      });

      // Reset input để chọn cùng tệp một lần nữa nếu cần
      this.value = '';
    };
  }

  // Event để xóa media cho create post
  $(document).on('click', '.remove-media', function () {
    $(this).closest('.create__post-media_item').remove();
    $('#create-post-video').val('');
    $('#create-post-img').val('');
    $('.file-comment').val('');
  });

  // Toggle Create Post Container
  function toggleCreatePostContainer() {
    const dialogForm = $('.dialog-container');
    const html = `
          <div class='dialog-wrapper'>
          <section class="create__post-post">
              <div class="post-title">
                <h3>Create Post</h3>
                <div id="close-dialog">&times;</div>
              </div>
                <form method="post" id="creat__post-form">
                    <textarea placeholder="What's on your mind ?" class="text-area" required></textarea>
                    <ul class="create__post-media_list">
                    </ul>
                    <div class=\"emoji-icon\">
                            <img src=\"../icon/smile.png\" alt=\"\" style=\"max-width:18px;\" class=\"post__emoji\">
                            <div class=\"emoji-selector\">
                                <div class='input-container'>
                                    <input placeholder=\"Seach...\" />
                                </div>
                                <div class=\"emoji-loading\">🔄 Đang tải emoji...</div>
                                <ul class=\"emoji-list\" id='emojiList'></ul>
                            </div>
                    </div>
                    <div class="create__post-options">
                        <p>Add to Your Post</p>
                        <ul class="create__post-option_list">
                            <li class="create__post-option_item">
                                <input type="file" id="create-post-img" accept=".jpg, .jpeg, .png" />
                                <label for="create-post-img">
                                    <img src="../icon/gallery.png" alt="" />
                                </label>
                            </li>
                            <li class="create__post-option_item">
                                <input id="create-post-video" type="file" accept="video/*" />
                                <label for="create-post-video">
                                    <img src="../icon/video.png" alt="" />
                                </label>
                            </li>
                        </ul>
                    </div>
                    <button type="button" id="create-post-btn">Post</button>
                </form>
            </section>
          </div>
          `;
    dialogForm.toggleClass('active');
    dialogForm.hasClass('active') ? dialogForm.html(html) : null;
  }

  // Khi người dùng nhấn ra ngoài creatPost thì ẩn đi
  $(window).click(function (event) {
    if ($(event.target).is('.dialog-container')) {
      toggleCreatePostContainer();
    }
  });

  // Toggle Share Post Container
  function toggleSharePostContainer(postId) {
    const dialogForm = $('.dialog-container');
    const html = `
          <div class='dialog-wrapper' >
          <section class="create__post-post">
              <div class="post-title">
                <h3>Share</h3>
                <div id="close-dialog">&times;</div>
              </div>
                <form method="post" id="creat__post-form">
                    <textarea placeholder="Say something about this ?!" class="text-area"></textarea>
                    <ul class="create__post-media_list">
                    </ul>
                    <div class=\"emoji-icon\" style="margin-bottom:10px">
                            <img src=\"../icon/smile.png\" alt=\"\" style=\"max-width:18px;\" class=\"post__emoji\">
                            <div class=\"emoji-selector\">
                                <div class='input-container'>
                                    <input placeholder=\"Seach...\" />
                                </div>
                                <div class=\"emoji-loading\">🔄 Đang tải emoji...</div>
                                <ul class=\"emoji-list\" id='emojiList'></ul>
                            </div>
                    </div>
                    <button type="button" id="share-post-btn" data-post-id='${postId}'>Share now</button>
                </form>
            </section>
          </div>
          `;
    dialogForm.html(html).toggleClass('active');
  }

  // Like Post
  $(document).on('click', '.like-btn', function () {
    const likeBtn = $(this); // Nút "Like" vừa được nhấn
    const postItem = likeBtn.closest('.post__box-post'); // Bài viết chứa nút "Like"
    const postId = postItem.data('post-id'); // Lấy post_id từ data-post-id

    // Gửi yêu cầu AJAX đến server
    $.ajax({
      url: '../ajax/home/post-handler.php', // Đường dẫn đến API xử lý like
      type: 'POST',
      data: {
        likePost: true,
        post_id: postId,
      },
      success: function (response) {
        const data = JSON.parse(response); // Chuyển đổi chuỗi JSON thành object

        // Kiểm tra kết quả trả về và cập nhật giao diện cho tất cả các bài viết có cùng postId
        if (data.status) {
          // Tìm tất cả các bài viết có cùng data-post-id
          $(`[data-post-id='${postId}']`).each(function () {
            const likeContainer = $(this).find('.post__box-like'); // Vùng chứa like
            const icon = $(this).find('.like-btn span i'); // Icon hiển thị trạng thái like
            const likeCount = likeContainer.find('span'); // Vùng hiển thị số lượt like

            // Cập nhật trạng thái like
            if (data.status === 'liked') {
              icon.removeClass('far fa-heart').addClass('fas fa-heart liked');
            } else if (data.status === 'unliked') {
              icon.removeClass('fas fa-heart liked').addClass('far fa-heart');
            }

            // Cập nhật số lượt like
            likeCount.text(data.likeCount); // Cập nhật số like
          });
        }
      },
      error: function () {
        alert('Không thể kết nối đến server!');
      },
    });
  });

  // Show Detail Post
  $('#post-list-form').on('click', '.comment-btn', function () {
    const postItem = $(this).closest('.post__box-post'); // Lấy bài viết đang nhấn vào
    const videoItem = postItem.find('video')[0];
    const postId = postItem.data('post-id'); // Lấy post_id của bài viết

    let currentTime = 0; // Lưu thời gian hiện tại của video

    if (videoItem) {
      currentTime = videoItem.currentTime; // Lưu thời gian phát video
      videoItem.pause(); // Tạm dừng video
    }

    // Gửi AJAX để lấy nội dung bài viết theo postId
    $.ajax({
      url: '../ajax/home/post-handler.php', // Đường dẫn tới script backend để lấy dữ liệu
      type: 'POST',
      data: { showPost: true, post_id: postId }, // Gửi post_id tới backend
      success: function (response) {
        // Nếu nhận được dữ liệu bài viết, hiển thị nó trong modal
        const postContent = $('#modal-post-content');
        postContent.html(response);

        // Tìm video mới sau khi mở Modal Post
        const newVideoItem = postContent.find('video')[0];
        // Kiểm tra nếu có video mới
        if (newVideoItem) {
          newVideoItem.muted = false; // Bật tiếng
          newVideoItem.currentTime = currentTime; // Đặt lại thời gian phát
          newVideoItem.play(); // Tiếp tục phát video
        }

        // Hiển thị modal với hiệu ứng fadeIn
        $('#post-modal').fadeIn(300); // 300ms là thời gian fadeIn
      },
      error: function () {
        alert('Không thể tải bài viết!');
      },
    });
  });

  // Show Share Post
  $(document).on('click', '.share-btn', function () {
    const postId = $(this).closest('.post__box-post').data('post-id');
    toggleSharePostContainer(postId);
  });

  // Share Post
  $(document).on('click', '#share-post-btn', function () {
    const postId = $(this).data('post-id');
    const content = $(this).closest('#creat__post-form').find('.text-area').val();

    $.ajax({
      url: '../ajax/home/post-handler.php',
      type: 'POST',
      data: { sharePost: true, postId: postId, content: content },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert(data.message);
        } else {
          alert('Lỗi: ' + data.message);
        }
      },
      complete: function () {
        $('.dialog-container').removeClass('active').empty();
      },
    });
  });

  // Khi người dùng nhấn ra ngoài modal, ẩn modal
  $(window).click(function (event) {
    if ($(event.target).is('#post-modal')) {
      $('#post-modal').fadeOut(300, function () {
        //Khi modal hoàn tất fadeOut, làm mới bài viết
        const postId = $('#modal-post-content .post__box-post').data('post-id'); // Lấy post_id từ phần tử post__box-post trong modal
        if (postId) {
          refreshPost(postId); // Gọi hàm làm mới bài viết
          $(document).on('mouseenter', '.post__box-comment_content', function () {
            const currentUserId = $('.header__info').data('user-id');
            const commentUserId = $(this).closest('.post__box-comment_item').data('user-id');
            if (currentUserId === commentUserId) {
              const commentId = $(this).closest('.post__box-comment_item').data('comment-id');
              const html = `
                <div class='menu-comment-btn' data-comment-id='${commentId}'>
                    <i class='ri-more-line'></i>
                </div>
            `;
              if (!$(this).find('.menu-comment-btn').length) {
                $(this).append(html);
              }
            }
          });
        }
      });
    }
  });

  // Hàm làm mới lại dữ liệu của bài viết sau khi mở modal
  function refreshPost(postId) {
    $.ajax({
      url: '../ajax/home/post-handler.php', // Đường dẫn tới script backend để lấy dữ liệu
      type: 'POST',
      data: { refreshPost: true, post_id: postId }, // Gửi post_id tới backend
      success: function (response) {
        // Cập nhật lại nội dung bài viết trong danh sách bài viết
        const postItem = $(`.post__box-post[data-post-id="${postId}"]`);
        postItem.replaceWith(response); // Thay thế bài viết bằng dữ liệu mới
      },
      error: function () {
        console.error('Không thể làm mới bài viết!');
      },
    });
  }

  // Toggle Menu Post
  $(document).on('click', '.menu-btn', function () {
    const self = $(this);
    const menuPost = self.find('.menu__post');

    // Kiểm tra nếu menu đã tồn tại
    if (menuPost.length > 0) {
      if (menuPost.is(':visible')) {
        menuPost.slideUp(100); // Ẩn menu với hiệu ứng slideUp
      } else {
        menuPost.slideDown(200); // Hiển thị menu với hiệu ứng slideDown
      }
    } else {
      const postId = self.closest('.post__box-post').data('post-id');
      const shareId = self.closest('.post__box-post').data('share-id');

      // Gửi yêu cầu AJAX để lấy menu
      $.ajax({
        url: '../ajax/home/post-handler.php',
        method: 'POST',
        data: {
          showMenuPost: true,
          postId: postId || null,
          shareId: shareId || null,
        },
        success: function (data) {
          // Thêm menu vào DOM và hiển thị
          self.append(data);
          const newMenu = self.find('.menu__post'); // Lần đầu gọi menu thì chưa có nên phải tạo biến mới
          newMenu.hide(); // Ẩn trước khi dùng slideDown
          newMenu.slideDown(200); // Hiển thị với hiệu ứng slideDown
        },
      });
    }
  });

  // Ẩn menuPost khi click ra ngoài
  $(document).on('click', function (e) {
    const menuPost = $('.menu__post');
    const menuButton = $('.menu-btn');

    // Kiểm tra nếu click không nằm trong menu hoặc nút menu
    if (!$(e.target).closest(menuPost).length && !$(e.target).closest(menuButton).length) {
      menuPost.slideUp(100); // Ẩn menu với hiệu ứng slideUp
    }
  });

  // Toggle Save Favorite Post
  $(document).on('click', '.save-post', function () {
    const self = $(this);
    const postId = self.closest('.post__box-post').data('post-id');

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { savePost: true, postId: postId },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          // Cập nhật giao diện (thay đổi nội dung nút thành "Hủy lưu bài viết" hoặc "Lưu bài viết")
          const text = data.is_saved ? 'Hủy lưu bài viết' : 'Lưu bài viết';
          const icon = data.is_saved ? 'ri-bookmark-2-fill' : 'ri-bookmark-fill';
          self.html(`<i class=${icon}></i> <span>${text}</span>`);
        } else {
          alert('Đã xảy ra lỗi, vui lòng thử lại.');
        }
      },
      error: function () {
        alert('Không thể kết nối đến máy chủ.');
      },
    });
  });

  // Show Edit Post
  $(document).on('click', '.edit-post', function () {
    const postId = $(this).closest('.post__box-post').data('post-id');
    const dialogForm = $('.dialog-container');

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { showEditPost: true, postId: postId },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            const post = data.response;
            const mediaUrl = post.media_url || '';
            let mediaHtml = '';

            if (mediaUrl) {
              const isImage = /\.(jpg|jpeg|png|gif)$/i.test(mediaUrl);
              const isVideo = /\.(mp4|webm|ogg)$/i.test(mediaUrl);
              mediaHtml = `
                <li class='create__post-media_item'>
                  <div class='media_item'>
                    ${
                      isImage
                        ? `<img src='${mediaUrl}' alt='Post media'>`
                        : isVideo
                        ? `<video src='${mediaUrl}'></video>`
                        : ''
                    }
                  <div class='remove-media'>&times;</div>
                  </div>
                </li>`;
            }

            const avatar = post.avatar ? post.avatar : '../img/default-avatar.png';

            const html = `
            <div class='dialog-wrapper' data-post-id='${postId}'>
              <section class="create__post-post">
                <div class="post-title">
                  <h3>Edit Post</h3>
                  <div id="close-dialog">&times;</div>
                </div>
                <form method="post" id="creat__post-form">
                  <div class="create__post-content">
                    <img src="${avatar}" alt='User avatar'>
                    <div class="create__post-details">
                      <span>${post.full_name}</span>
                    </div>
                  </div>
                  <textarea class="text-area" placeholder="What's on your mind, ${post.full_name}?" rows="5" required>${post.content}</textarea>
                  <ul class="create__post-media_list">${mediaHtml}</ul>
                  <div class=\"emoji-icon\">
                            <img src=\"../icon/smile.png\" alt=\"\" style=\"max-width:18px;\" class=\"post__emoji\">
                            <div class=\"emoji-selector\">
                                <div class='input-container'>
                                    <input placeholder=\"Seach...\" />
                                </div>
                                <div class=\"emoji-loading\">🔄 Đang tải emoji...</div>
                                <ul class=\"emoji-list\" id='emojiList'></ul>
                            </div>
                  </div>
                  <div class="create__post-options">
                    <p>Add to Your Post</p>
                    <ul class="create__post-option_list">
                      <li class="create__post-option_item">
                        <input type="file" id="create-post-img" accept=".jpg, .jpeg, .png">
                        <label for="create-post-img">
                          <img src="../icon/gallery.png" alt="">
                        </label>
                      </li>
                      <li class="create__post-option_item">
                        <input id="create-post-video" type="file" accept="video/*">
                        <label for="create-post-video">
                          <img src="../icon/video.png" alt="">
                        </label>
                      </li>
                    </ul>
                  </div>
                  <button type="button" id="save-post-button">Save</button>
                </form>
              </section>
            </div>`;

            dialogForm.html(html).addClass('active');
          } else {
            alert('Failed to fetch post data!');
          }
        } catch (error) {
          alert('Error parsing response data!');
          console.error(error);
        }
      },
      error: function () {
        alert('An error occurred while processing your request.');
      },
    });
  });

  // Save Edit Post
  $(document).on('click', '#save-post-button', async function () {
    const dialogWrapper = $(this).closest('.dialog-wrapper');
    const postId = dialogWrapper.data('post-id');
    const content = dialogWrapper.find('.text-area').val();
    const mediaList = dialogWrapper.find('.create__post-media_list');

    // Kiểm tra nội dung có thay đổi không
    const originalContent = dialogWrapper.find('.text-area').attr('placeholder');
    const isContentChanged = content.trim() !== originalContent.trim();

    // Kiểm tra file input (chỉ cho phép 1 file)
    const imageFile = $('#create-post-img')[0].files[0];
    const videoFile = $('#create-post-video')[0].files[0];
    let mediaUrl = '';

    // Kiểm tra nếu người dùng xóa media
    const isMediaDeleted = mediaList.find('.create__post-media_item').length === 0;

    // Nếu không có thay đổi thì không gửi request
    if (!isContentChanged && !imageFile && !videoFile && !isMediaDeleted) {
      alert('No changes detected.');
      return;
    }

    // Nếu có file mới, upload lên Cloudinary
    if (imageFile) {
      try {
        $('.overlay').addClass('active');
        mediaUrl = await uploadToCloudinary(imageFile, 'img');
      } catch (error) {
        $('.overlay').removeClass('active');
        alert('Image upload failed.');
        return;
      }
    } else if (videoFile) {
      try {
        mediaUrl = await uploadToCloudinary(videoFile, 'video');
      } catch (error) {
        $('.overlay').removeClass('active');
        alert('Video upload failed.');
        return;
      }
    }

    // Chuẩn bị dữ liệu gửi lên server
    const formData = new FormData();
    formData.append('editPost', true);
    formData.append('postId', postId);
    formData.append('content', content);

    if (mediaUrl) {
      formData.append('mediaUrl', mediaUrl);
    } else if (isMediaDeleted) {
      formData.append('deleteMedia', true);
    }

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const res = JSON.parse(response);
        if (res.success) {
          // Cập nhật nội dung bài viết trên giao diện
          $(`[data-post-id="${postId}"] .post__box-text`).text(res.content);

          // KIỂM TRA MEDIA TRẢ VỀ
          // Kiểm tra nếu có media
          if (res.mediaUrl) {
            const imageRegex = /\.(jpg|jpeg|png|gif)$/i; // Biểu thức chính quy kiểm tra hình ảnh
            const videoRegex = /\.(mp4|webm|ogg)$/i; // Biểu thức chính quy kiểm tra video
            let mediaHtml = '';

            // Kiểm tra xem có phải là hình ảnh
            if (imageRegex.test(res.mediaUrl)) {
              mediaHtml = `<img src="${res.mediaUrl}" alt="Post Media">`;
            }
            // Kiểm tra xem có phải là video
            else if (videoRegex.test(res.mediaUrl)) {
              mediaHtml = `<video controls muted><source src="${res.mediaUrl}" type="video/${res.mediaUrl
                .split('.')
                .pop()}"></video>`;
            }

            // Cập nhật phần media
            $(`[data-post-id="${postId}"] .post__box-media_list`).html(mediaHtml);
          } else {
            // Nếu không có media, xóa phần media (nếu có)
            $(`[data-post-id="${postId}"] .post__box-media_list`).empty();
          }
        } else {
          alert(res.message || 'Failed to update the post.');
        }
      },
      complete: function () {
        clearCreatePost();
      },
      error: function () {
        alert('An error occurred while processing your request.');
      },
    });
  });

  // Show Confirm Delete Post
  $(document).on('click', '.delete-post', function () {
    const postId = $(this).closest('.post__box-post').data('post-id');
    const dialogForm = $('.dialog-container');
    const html = `
      <div class="dialog-wrapper" data-post-id = '${postId}'>
        <div class="dialog-header">
          Delete your post?
        </div>
        <div class="dialog-content">Post will be deleted ?</div>
        <div class="dialog-actions">
          <button class="cancel-button">Cancel</button>
          <button id="delete-post-btn">Delete</button>
        </div>
      </div>
    `;
    dialogForm.html(html);
    dialogForm.addClass('active');
  });

  // Submit Delete Post
  $(document).on('click', '#delete-post-btn', function () {
    const postId = $(this).closest('.dialog-wrapper').data('post-id'); // Lấy postId từ dialog
    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { deletePost: true, postId: postId },
      success: function (data) {
        // Kiểm tra nếu xóa thành công
        if (data === 'true') {
          // Xóa thẻ .post__box-post với id là postId
          $(`[data-post-id="${postId}"]`).remove(); // Tìm và xóa phần tử có data-post-id tương ứng
          $('.dialog-container').removeClass('active').empty(); // Đóng hộp thoại sau khi xóa

          // Kiểm tra nếu #post-modal đang hiển thị
          if ($('#post-modal').is(':visible')) {
            $('#post-modal').hide();
          }
        } else {
          alert('Something went wrong! Post could not be deleted.');
        }
      },
      error: function () {
        alert('Error occurred while deleting the post.');
      },
    });
  });

  // Chức năng chỉnh sửa bài chia sẻ
  $(document).on('click', '.edit-share', function () {
    const shareId = $(this).closest('.post__box-post').data('share-id');
    const dialogForm = $('.dialog-container');

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { showEditShare: true, shareId: shareId },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            const post = data.response;

            const avatar = post.avatar ? post.avatar : '../img/default-avatar.png';

            const html = `
            <div class='dialog-wrapper' data-share-id='${shareId}'>
              <section class="create__post-post">
                <div class="post-title">
                  <h3>Edit Share</h3>
                  <div id="close-dialog">&times;</div>
                </div>
                <form method="post" id="creat__post-form">
                  <div class="create__post-content">
                    <img src="${avatar}" alt='User avatar'>
                    <div class="create__post-details">
                      <span>${post.full_name}</span>
                    </div>
                  </div>
                  <textarea class="text-area" placeholder="What's on your mind, ${post.full_name}?" rows="5" required>${post.content}</textarea>
                  <div class=\"emoji-icon\">
                            <img src=\"../icon/smile.png\" alt=\"\" style=\"max-width:18px;\" class=\"post__emoji\">
                            <div class=\"emoji-selector\">
                                <div class='input-container'>
                                    <input placeholder=\"Seach...\" />
                                </div>
                                <div class=\"emoji-loading\">🔄 Đang tải emoji...</div>
                                <ul class=\"emoji-list\" id='emojiList'></ul>
                            </div>
                  </div>
                  <button type="button" id="save-share-button">Save</button>
                </form>
              </section>
            </div>`;

            dialogForm.html(html).addClass('active');
          } else {
            alert('Failed to fetch post data!');
          }
        } catch (error) {
          alert('Error parsing response data!');
          console.error(error);
        }
      },
      error: function () {
        alert('An error occurred while processing your request.');
      },
    });
  });

  // Chức năng xác nhận lưu chỉnh sửa bài chia sẻ
  $(document).on('click', '#save-share-button', function () {
    const dialogWrapper = $(this).closest('.dialog-wrapper');
    const shareId = dialogWrapper.data('share-id');
    const content = dialogWrapper.find('.text-area').val();

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { editShare: true, shareId: shareId, content: content },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          // Cập nhật nội dung bài viết trên giao diện
          $(`[data-share-id="${shareId}"] .post__box-text`).first().text(data.content);
        } else {
          alert(data.message || 'Failed to update the post.');
        }
      },
      complete: function () {
        clearCreatePost();
      },
      error: function () {
        alert('An error occurred while processing your request.');
      },
    });
  });

  // Chức năng hiện thông báo về xóa bài chia sẻ
  $(document).on('click', '.delete-share', function () {
    const shareId = $(this).closest('.post__box-post').data('share-id');
    const dialogForm = $('.dialog-container');
    const html = `
      <div class="dialog-wrapper" data-share-id = '${shareId}'>
        <div class="dialog-header">
          Delete your share?
        </div>
        <div class="dialog-content">Share will be deleted ?</div>
        <div class="dialog-actions">
          <button class="cancel-button">Cancel</button>
          <button id="delete-share-btn">Delete</button>
        </div>
      </div>
    `;
    dialogForm.html(html);
    dialogForm.addClass('active');
  });

  // Chức năng xác nhận xóa bài chia sẻ
  // Submit Delete Post
  $(document).on('click', '#delete-share-btn', function () {
    const shareId = $(this).closest('.dialog-wrapper').data('share-id');
    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { deleteShare: true, shareId: shareId },
      success: function (data) {
        // Kiểm tra nếu xóa thành công
        if (data) {
          // Xóa thẻ .post__box-post với id là shareId
          $(`[data-share-id="${shareId}"]`).remove();
          $('.dialog-container').removeClass('active').empty(); // Đóng hộp thoại sau khi xóa
        } else {
          alert('Something went wrong! Post could not be deleted.');
        }
      },
      error: function () {
        alert('Error occurred while deleting the post.');
      },
    });
  });

  // Read More Content
  $(document).on('click', '.read-more', function () {
    const textElement = $(this).prev('.post__box-text');
    const fullContent = textElement.data('full-content');
    textElement.text(fullContent);
    textElement.addClass('expanded');
    $(this).hide();
  });
  // Clear CreatePost Container
  function clearCreatePost() {
    $('.dialog-container').removeClass('active').empty();
    $('.overlay').removeClass('active');
  }

  // Hiện danh sách user đã like Post
  $(document).on('click', '.post__box-like', function () {
    const postId = $(this).closest('.post__box-post').data('post-id');
    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { fetchUsersLike: true, postId: postId },
      success: function (data) {
        const dialogForm = $('.dialog-container');
        const html = `
          <div class='dialog-wrapper'>
            <p style='text-align:center; font-size:17px;font-weight:600;'>Danh sách Like</p>
            <div class='users-like'>
              ${data}
            </div>
          </div>
        `;
        dialogForm.html(html).addClass('active');
      },
    });
  });

  // Get Post By ID
  function getPost() {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get('id');

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: { getPost: true, postId: postId },
      success: function (data) {
        $('#post-container').html(data);
      },
    });
  }

  getPost();

  // Các biến dùng để lấy bài viết về
  let lastCreatedAt = null;
  const limit = 10;
  let isLoading = false;
  let hasMore = true;

  // Hàm fetchData tổng quát
  function fetchData(action, userId = null, append = false) {
    if (isLoading || !hasMore) return;

    isLoading = true;

    const requestData = {
      limit: limit,
      last_created_at: lastCreatedAt,
    };

    if (action === 'fetchPost') {
      requestData.fetchPost = true;
    } else if (action === 'fetchPostsAndShares') {
      requestData.fetchPostsAndShares = true;
      requestData.userId = userId;
    }

    $.ajax({
      url: '../ajax/home/post-handler.php',
      method: 'POST',
      data: requestData,
      success: function (response) {
        const data = JSON.parse(response);
        // Thêm bài viết vào danh sách nếu append = null, còn nếu append = true thì thêm vào cuối form
        if (append) {
          $('#post-list-form').append(data.html);
        } else {
          $('#post-list-form').html(data.html);
        }
        lastCreatedAt = data.last_created_at;
        hasMore = data.has_more;
        isLoading = false;
      },
      error: function () {
        isLoading = false;
      },
    });
  }

  // Get Posts
  function fetchPost(append = null) {
    lastCreatedAt = null;
    hasMore = true;
    fetchData('fetchPost');
  }

  fetchPost();

  // Lấy danh sách các bài viết và bài chia sẻ của người dùng theo user_id
  function fetchPostsAndShares(userId) {
    lastCreatedAt = null;
    hasMore = true;
    fetchData('fetchPostsAndShares', userId);
  }

  // Hiển thị thêm bài viết khi người dùng lướt đến cuối
  $(document).on('scroll', function () {
    // Nếu người dùng đang xem trang cuối cùng của trang web thì thực hiện lấy bài viết tiếp theo
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
      const action = window.location.pathname.includes('profile.php') ? 'fetchPostsAndShares' : 'fetchPost';
      const userId = action === 'fetchPostsAndShares' ? getUserIdFromUrl() : null; // Lấy userId từ URL nếu cần
      fetchData(action, userId, true); // Tải thêm khi cuộn
    }
  });

  // Hàm hỗ trợ lấy userId từ URL
  // function getUserIdFromUrl() {
  //   const urlParams = new URLSearchParams(window.location.search);
  //   return urlParams.get('user_id');
  // }
});
