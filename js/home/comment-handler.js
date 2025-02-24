$(document).ready(function () {
  // Send Comment Post -> Nhan Enter
  $(document).on('keypress', '.comment__input', function (event) {
    if (event.which === 13 && !event.shiftKey) {
      // Kiểm tra nếu nhấn Enter nhưng không nhấn Shift
      event.preventDefault(); // Ngăn chặn xuống dòng trong input
      $(this).closest('.comment__box-wrapper').find('.comment__submit').click(); // Gọi sự kiện click
    }
  });

  // Send Comment Post
  $(document).on('click', '.comment__submit', async function () {
    const self = this; // Lưu tham chiếu đến nút hiện tại
    const postId = $(self).closest('.post__box-post').data('post-id'); // Lấy ID bài viết
    const currentUserId = $('.header__info').data('user-id');
    const commentInput = $(self).closest('.comment__box-wrapper').find('.comment__input'); // Input của bình luận
    const commentText = commentInput.val().trim(); // Lấy nội dung bình luận
    const fileInput = $(self).closest('.comment__box-wrapper').find('.file-comment')[0]; // Lấy file ảnh
    let media_url = null;
    // Kiểm tra nếu nội dung bình luận rỗng
    if (!commentText) {
      alert('Please enter a comment!');
      return;
    }

    // Kiểm tra và tải file lên nếu có
    if (fileInput && fileInput.files.length > 0) {
      const file = fileInput.files[0]; // Lấy file object
      try {
        $('.overlay').addClass('active'); // Hiện overlay khi tải lên
        media_url = await uploadToCloudinary(file); // Đợi tải file lên Cloudinary
      } catch (error) {
        alert('Error uploading file. Please try again.');
        $('.overlay').removeClass('active');
        return;
      }
    }

    // Gửi bình luận
    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: {
        sendComment: true,
        postId: postId,
        commentText: commentText,
        commentImg: media_url,
      },
      success: function (response) {
        const data = JSON.parse(response);

        if (data.success) {
          // Thêm bình luận mới vào danh sách bình luận
          const commentList = $(`[data-post-id="${postId}"] .post__box-comment_list`);

          const imgElement = data.commentImg ? `<img src="${data.commentImg}" alt="" class="comment-img">` : '';

          const newComment = `
                        <li class="post__box-comment_item" data-comment-id="${data.commentId}" data-user-id="${currentUserId}">
                            <div class="post__box-comment_content">
                                <img src="${data.userAvatar}" alt="${data.userName}" class="comment-avatar">
                                <div class="post__box-comment_details">
                                    <div class="post__box-comment_text">
                                        <span>${data.userName}</span>
                                        <div>${data.commentText}</div>
                                    </div>
                                    ${imgElement}
                                    <div class="post__box-comment_interaction">
                                        <div class="post__box-action">
                                            <span>${data.createdAt}</span>
                                            <span class="like-comment-btn">Like</span>
                                            <span class="reply-btn">Reply</span>
                                            <span class="total-like">${data.totalLike} <i class="fas fa-heart" style="color:red"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `;

          // Chèn bình luận mới lên đầu danh sách
          commentList.prepend(newComment);

          // Xóa nội dung trong input sau khi gửi thành công
          commentInput.val('');

          // Xóa ảnh trong comment box (nếu có)
          $(self).closest('.comment__box-wrapper').find('.comment__img').remove();
        } else {
          alert(data.message || 'Cannot post the comment. Please try again.');
        }
      },
      error: function () {
        alert('Error connecting to server. Please try again.');
      },
      complete: function () {
        $('.overlay').removeClass('active');
      },
    });
  });

  // Like Commment
  $(document).on('click', '.like-comment-btn', function () {
    const likeButton = $(this);
    const commentId = likeButton.closest('.post__box-comment_item').data('comment-id');

    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: { likeComment: true, commentId: commentId },
      success: function (response) {
        const data = JSON.parse(response);

        if (data.success) {
          // Cập nhật trạng thái nút Like
          if (data.userHasLiked) {
            likeButton.addClass('comment-liked'); // Đổi màu hoặc biểu tượng khi đã like
          } else {
            likeButton.removeClass('comment-liked'); // Trở lại trạng thái chưa like
          }

          // Cập nhật số lượng like hiển thị
          likeButton.siblings('.total-like').html(`${data.likeCount} <i class="fas fa-heart" style="color:red"></i>`);
        } else {
          alert(data.message || 'Error updating like status.');
        }
      },
      error: function () {
        alert('Error connecting to the server.');
      },
    });
  });

  // Reply Comment -> Nhan Enter
  $(document).on('keypress', '.comment__input', function (event) {
    if (event.which === 13 && !event.shiftKey) {
      // Kiểm tra nếu nhấn Enter nhưng không nhấn Shift
      event.preventDefault(); // Ngăn chặn xuống dòng trong input
      $(this).closest('.comment__box-wrapper').find('.reply__submit').click(); // Gọi sự kiện click
    }
  });

  // Reply Comment
  $(document).on('click', '.reply__submit', async function () {
    const self = this; // Lưu tham chiếu đến nút hiện tại
    const postId = $(self).closest('.post__box-post').data('post-id'); // Lấy ID bài viết
    const currentUserId = $('.header__info').data('user-id');
    const commentItem = $(self).closest('.post__box-comment_item');
    const parentId = commentItem.data('comment-id') || null; // Lấy ID bình luận cha
    const commentInput = $(self).closest('.comment__box-wrapper').find('.comment__input'); // Input của bình luận
    const commentText = commentInput.val().trim(); // Lấy nội dung bình luận
    const fileInput = $(self).closest('.comment__box-wrapper').find('.file-comment')[0]; // Lấy file ảnh
    let media_url = null;
    // Kiểm tra nếu nội dung bình luận rỗng
    if (!commentText) {
      alert('Please enter a comment!');
      return;
    }

    // Kiểm tra và tải file lên nếu có
    if (fileInput && fileInput.files.length > 0) {
      const file = fileInput.files[0]; // Lấy file object
      $('.overlay').addClass('active'); // Hiện overlay khi tải lên

      try {
        media_url = await uploadToCloudinary(file); // Đợi tải file lên Cloudinary
      } catch (error) {
        alert('Error uploading file. Please try again.');
        $('.overlay').removeClass('active');
        return;
      }
    }

    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: { sendReply: true, postId: postId, parentId: parentId, commentText: commentText, commentImg: media_url },
      success: function (response) {
        const data = JSON.parse(response);

        if (data.success) {
          const imgElement = data.commentImg != '' ? `<img src="${data.commentImg}" alt="" class="comment-img">` : '';

          const newComment = `
                    <li class="post__box-comment_item" data-comment-id="${data.commentId}" data-user-id="${currentUserId}">
                        <div class="post__box-comment_content">
                            <img src="${data.userAvatar}" alt="${data.userName}" class='comment-avatar'>
                            <div class="post__box-comment_details">
                                <div class="post__box-comment_text">
                                    <span>${data.userName}</span>
                                    <div>${data.commentText}</div>
                                </div>
                                ${imgElement}
                                <div class="post__box-comment_interaction">
                                    <div class="post__box-action">
                                        <span>${data.createdAt}</span>
                                        <span class='like-comment-btn'>Like</span>
                                        <span class="reply-btn">Reply</span>
                                        <span class='total-like'>${data.totalLike} <i class="fas fa-heart" style="color:red"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                `;

          // Kiểm tra nếu đã có ul trước .comment__box, nếu không thì tạo mới
          let commentList = commentItem.find('ul.post__box-comment_list-temp');
          if (commentList.length === 0) {
            commentList = $('<ul class="post__box-comment_list-temp"></ul>'); // Tạo ul mới
            commentItem.find('.comment__box').before(commentList); // Chèn ul ngay trước .comment__box
          }

          // Thêm bình luận mới vào ul
          commentList.append(newComment);

          // Reset input sau khi gửi bình luận
          commentInput.val('');
          // Xóa ảnh trong comment box (nếu có)
          $(self).closest('.comment__box-wrapper').find('.comment__img').remove();
        } else {
          alert('Failed to add reply');
        }
      },
      error: function () {
        alert('Could not connect to the server!');
      },
      complete: function () {
        $('.overlay').removeClass('active');
      },
    });
  });

  // Người dùng chọn ảnh cho comment
  $(document).on('change', '.file-comment', function (event) {
    const file = event.target.files[0]; // Lấy file đầu tiên

    if (file) {
      // Sử dụng URL.createObjectURL để tạo URL tạm thời cho file
      const imgSrc = URL.createObjectURL(file);

      // Tạo HTML cho ảnh đã chọn
      const imageHtml = `
            <div class="comment__img" style="display: block;">
                <img src="${imgSrc}" class="comment__img-preview" alt="Image Preview" />
                <div class="remove-media">&times;</div>
            </div>
        `;

      // Tìm phần tử cha gần nhất
      const commentBoxWrapper = $(this).closest('.comment__box-wrapper');

      // Kiểm tra xem có phần tử ảnh cũ hay chưa
      if (commentBoxWrapper.find('.comment__img').length) {
        // Nếu tồn tại, thay thế ảnh cũ bằng ảnh mới
        commentBoxWrapper.find('.comment__img').replaceWith(imageHtml);
      } else {
        // Nếu chưa có, thêm mới vào
        commentBoxWrapper.append(imageHtml);
      }
    }
  });

  // Xử lý khi người dùng nhấn "×" để xóa ảnh đã chọn cho comment
  $(document).on('click', '.remove-media', function () {
    const commentImgDiv = $(this).closest('.comment__img');
    commentImgDiv.remove(); // Xóa phần tử ảnh
    $(this).closest('.comment__box').find('.file-comment').val(''); // Reset file đã chọn
  });

  // Hàm kích hoạt sự kiện mouseenter cho bình luận
  function enableCommentMenu() {
    $(document).on('mouseenter', '.post__box-comment_content', function () {
      const currentUserId = $('.header__info').data('user-id');
      const commentItem = $(this).closest('.post__box-comment_item').first();
      const commentId = commentItem.data('comment-id');
      const commentUserId = commentItem.data('user-id');

      if (currentUserId === commentUserId) {
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

  // Gọi hàm ngay khi trang tải
  enableCommentMenu();

  // Hide Button Menu Comment
  $(document).on('mouseleave', '.post__box-comment_content', function () {
    $(this).find('.menu-comment-btn').remove();
  });

  // Hiển thị hoặc ẩn danh sách menu Comment khi nhấn vào nút menu
  $(document).on('click', '.menu-comment-btn', function () {
    const self = $(this);
    const commentId = self.closest('.menu-comment-btn').data('comment-id'); // Lấy commentId từ menu
    const commentItem = $(`.post__box-comment_item[data-comment-id='${commentId}']`);
    const parent = commentItem.find('.post__box-comment_content'); // Lấy phần tử cha

    let menuList = parent.find('.menu__comment'); // Tìm danh sách menu nếu đã tồn tại

    // Nếu chưa có menu thì thêm mới
    if (!menuList.length) {
      const html = `
      <div class='menu__comment'>
        <ul class='menu__comment-list'>
          <li class='menu__comment-item edit-comment'><span>Chỉnh sửa bình luận</span></li>
          <li class='menu__comment-item delete-comment'><span>Xóa bình luận</span></li>
        </ul>
      </div>`;
      self.append(html); // Thêm menu ngay sau `.menu-comment`
      menuList = parent.find('.menu__comment'); // Cập nhật biến menuList
    }

    // Toggle hiển thị/ẩn menu với slideDown và slideUp
    if (menuList.is(':visible')) {
      menuList.slideUp(200); // Ẩn menu
    } else {
      menuList.slideDown(200); // Hiển thị menu
    }
  });

  // Show Edit Comment Box
  $(document).on('click', '.edit-comment', function () {
    const self = $(this);
    const commentId = self.closest('.menu-comment-btn').data('comment-id'); // Lấy commentId từ menu

    // Tìm đúng .post__box-comment_item với commentId tương ứng
    const commentItem = $(`.post__box-comment_item[data-comment-id='${commentId}']`);

    if (!commentItem.length) return; // Nếu không tìm thấy thì thoát

    // Tạm thời vô hiệu hóa sự kiện mouseenter
    $(document).off('mouseenter', '.post__box-comment_content');
    setTimeout(function () {
      $('.menu-comment-btn').remove();
    }, 50);

    // Lưu lại HTML gốc của .post__box-comment_details
    const commentDetails = commentItem.find('.post__box-comment_details').first();
    const originalHtml = commentDetails.html();
    commentDetails.data('original-html', originalHtml); // Lưu trữ HTML ban đầu trong data

    // Text của comment
    const commentText = commentItem.find('.post__box-comment_text div').first().text();
    // Url ảnh của comment (nếu có)
    const imgUrl = commentItem.find('.comment-img').attr('src');
    let imageHtml = ``;
    if (imgUrl) {
      imageHtml = `
            <div class="comment__img" style="display: block;">
                <img src="${imgUrl}" class="comment__img-preview" alt="Image Preview" />
                <div class="remove-media">&times;</div>
            </div>
        `;
    }

    // Thay đổi giao diện để hiển thị textarea
    const html = `
        <div class='comment__box-wrapper' style="width:650px;">
                        <textarea type='text' class='comment__input' rows='2' cols='50' style='resize: none;' placeholder='Comment...'>${commentText}</textarea>
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
                        <div class='comment__box-action'>
                            <span class='comment__media'>
                                <label class="custom-file-label">
                                  <i class="fas fa-camera-retro"></i>
                                  <input type="file" class="file-comment" accept=".jpg, .jpeg, .png" />
                                </label>
                            </span>
                            <span id='save-edit-comment'>
                              <i class='fas fa-paper-plane'></i>
                            </span>
                        </div>
                        ${imageHtml}
                    </div>
                    <div style="font-size: 11px;margin-top: 3px;">Nhấn <span style="color:#3080eb;">Esc</span> để thoát</div>`;
    commentDetails.html(html);

    // Thêm sự kiện lắng nghe phím Escape khi người dùng không chỉnh sửa nữa
    // Gán sự kiện keydown cho textarea của comment đang chỉnh sửa
    commentDetails.find('.comment__input').focus();
    commentDetails.find('.comment__input').on('keydown', function (event) {
      if (event.key === 'Escape') {
        // Chỉ khôi phục nội dung của comment hiện tại
        commentDetails.html(commentDetails.data('original-html'));

        // Kích hoạt lại sự kiện mouseenter
        enableCommentMenu();

        // Xóa sự kiện keydown chỉ trong textarea này
        $(this).off('keydown');
      }
    });
  });

  // Save Edited Comment -> Nhan Enter
  $(document).on('keypress', '.comment__input', function (event) {
    if (event.which === 13 && !event.shiftKey) {
      // Kiểm tra nếu nhấn Enter nhưng không nhấn Shift
      event.preventDefault(); // Ngăn chặn xuống dòng trong input
      $(this).closest('.comment__box-wrapper').find('#save-edit-comment').click(); // Gọi sự kiện click
    }
  });

  // Save Edited Comment
  $(document).on('click', '#save-edit-comment', async function () {
    const self = $(this);
    const commentWrapper = self.closest('.comment__box-wrapper');
    const newComment = commentWrapper.find('.comment__input').val();
    const commentItem = self.closest('.post__box-comment_item');
    const commentId = commentItem.data('comment-id');

    const newImageFile = commentWrapper.find('.file-comment')[0].files[0]; // Ảnh mới
    let mediaUrl = ''; // URL ảnh mới sau khi upload
    let isMediaDeleted = false; // Trạng thái ảnh bị xóa

    // Kiểm tra nếu ảnh cũ tồn tại nhưng đã bị xóa khỏi giao diện
    if (commentWrapper.find('.comment__img').length === 0) {
      isMediaDeleted = true;
    }

    // Nếu có ảnh mới, upload lên Cloudinary
    if (newImageFile) {
      try {
        $('.overlay').addClass('active'); // Xoay vong khi load binh luan
        mediaUrl = await uploadToCloudinary(newImageFile, 'img');
      } catch (error) {
        $('.overlay').removeClass('active');
        alert('Image upload failed.');
        return;
      }
    }

    // Chuẩn bị dữ liệu gửi lên server
    const requestData = {
      saveEditedComment: true,
      commentId: commentId,
      content: newComment,
    };

    if (mediaUrl) {
      requestData.mediaUrl = mediaUrl;
    } else if (isMediaDeleted) {
      requestData.deleteMedia = true;
    }

    // Gửi Ajax lên server
    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: requestData,
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          const commentData = data.commentData;
          const commentDetails = commentItem.find('.post__box-comment_details').first();
          const imgElement = commentData.comment_img
            ? `<img src="${commentData.comment_img}" alt="" class="comment-img">`
            : '';
          const isLiked = commentData.user_has_liked ? 'comment-liked' : '';

          commentDetails.html(`
                    <div class='post__box-comment_text'>
                        <span>${commentData.full_name}</span>
                        <div>${newComment}</div>
                    </div>
                    ${imgElement}
                    <div class='post__box-comment_interaction'>
                        <div class='post__box-action'>
                            <span>${formatTime(commentData.created_at)}</span>
                            <span class='like-comment-btn ${isLiked}'>Like</span>
                            <span class='reply-btn'>Reply</span>
                            <span class='total-like'>
                                ${commentData.total_like} <i class="fas fa-heart" style="color:red"></i>
                            </span>
                        </div>
                    </div>
                `);

          // Kích hoạt lại sự kiện mouseenter
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
        } else {
          alert(data.message || 'Failed to update the comment.');
        }
      },
      complete: function () {
        $('.overlay').removeClass('active');
      },
      error: function () {
        alert('An error occurred while processing your request.');
      },
    });
  });

  // Show Delete Comment Dialog
  $(document).on('click', '.delete-comment', function () {
    const commentId = $(this).closest('.post__box-comment_item').data('comment-id');
    const dialogForm = $('.dialog-container');
    const html = `
      <div class="dialog-wrapper" data-comment-id = '${commentId}'>
        <div class="dialog-header">
          Delete comment?
        </div>
        <div class="dialog-content">This comment will be deleted?</div>
        <div class="dialog-actions">
          <button class="cancel-button">Cancel</button>
          <button id="delete-comment-btn">Delete</button>
        </div>
      </div>
    `;
    dialogForm.html(html);
    dialogForm.addClass('active');
  });

  // Submit Delete Comment
  $(document).on('click', '#delete-comment-btn', function () {
    const commentId = $(this).closest('.dialog-wrapper').data('comment-id'); // Lấy postId từ dialog
    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: { deleteComment: true, commentId: commentId },
      success: function (data) {
        // Kiểm tra nếu xóa thành công
        if (data === 'true') {
          // Xóa thẻ .post__box-post với id là postId
          $(`[data-comment-id="${commentId}"]`).remove(); // Tìm và xóa phần tử có data-post-id tương ứng
        } else {
          alert('Something went wrong! Post could not be deleted.');
        }
      },
      complete: function () {
        $('.dialog-container').removeClass('active').empty();
      },
      error: function () {
        alert('Error occurred while deleting the post.');
      },
    });
  });

  // Load More Main Comments
  $(document).on('click', '#load-more-btn', function () {
    const button = $(this);
    const postId = button.closest('.post__box-post').data('post-id'); // Lấy ID bài viết
    const lastComment = $('.post__box-comment_list > .post__box-comment_item').last();
    // Kiểm tra nếu không còn bình luận nào trong danh sách
    let lastCreatedAt = lastComment.length > 0 ? lastComment.data('created-at') : null;
    const limit = 2;

    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: { loadMoreMainComment: true, postId: postId, limit: limit, lastCreatedAt: lastCreatedAt },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          // Tạo một container tạm cho các bình luận mới
          const $newComments = $(data.html).hide(); // Ẩn các bình luận mới
          $('.post__box-comment_list').append($newComments); // Chèn vào danh sách bình luận
          $newComments.slideDown(); // Thêm hiệu ứng trượt xuống

          // Cập nhật offset

          // Kiểm tra nếu không còn bình luận nào để tải
          if (!data.hasMore) {
            button.hide(); // Ẩn nút "Tải thêm bình luận"
          }
        } else {
          alert('Không thể tải thêm bình luận');
        }
      },
      error: function () {
        alert('Lỗi khi kết nối với máy chủ!');
      },
    });
  });

  // Show Children Comments
  $(document).on('click', '.view-replies-btn', function () {
    const button = $(this); // Lấy nút hiện tại
    const commentItem = button.closest('.post__box-comment_item'); // Lấy comment hiện tại
    const parentId = button.data('parent-id'); // Lấy ID của bình luận cha
    let repliesContainer = commentItem.find('.post__box-comment_reply');
    // Xoá các comment tạm thời người dùng đã gửi , khi chưa mở "xem tất cả"
    commentItem.find('ul.post__box-comment_list-temp').empty();

    if (repliesContainer.length === 0) {
      repliesContainer = $('<ul class="post__box-comment_reply"></ul>');
      button.before(repliesContainer); // Thêm ul ngay trước nút "Xem các bình luận"
    }

    // Lấy created_at của comment cuối cùng trong danh sách (nếu có)
    let lastCreatedAt = repliesContainer.find('.post__box-comment_item').last().data('created-at') || null;

    const limit = 5;

    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: { loadReplies: true, parentId: parentId, lastCreatedAt: lastCreatedAt, limit: limit },
      success: function (response) {
        const data = JSON.parse(response);

        if (data.success) {
          // Tạo HTML từ dữ liệu trả về
          const repliesHtml = renderComments(data.replies);
          const newReplies = $(repliesHtml);

          // Chèn vào trước nút "Xem các bình luận"
          repliesContainer.append(newReplies);
          newReplies.hide().slideDown();

          // Kiểm tra nếu hết bình luận reply thì ẩn nút
          if (!data.hasMore) {
            button.hide();
          }
        } else {
          alert('Không thể tải bình luận');
        }
      },
      error: function () {
        alert('Không thể kết nối tới máy chủ!');
      },
    });
  });

  // Hàm renderMoreReplyComments
  function renderComments(comments) {
    let html = '';
    comments.forEach((comment) => {
      const userId = comment.user_id;
      const username = comment.username;
      const avatar = comment.user_avatar || '../img/default-avatar.png';
      const text = comment.comment_text;
      const img = comment.comment_img;
      const likeCount = comment.like_count || 0;
      const createdAt = formatTime(comment.created_at);
      const hasReplies = comment.has_replies;
      const imgElement = img != '' ? `<img src=${img} alt="" class="comment-img">` : '';
      const isLiked = comment.user_has_liked ? 'comment-liked' : '';
      html += `
            <li class="post__box-comment_item" data-comment-id="${comment.comment_id}"
            data-user-id="${userId}" data-created-at="${comment.created_at}">
                <div class="post__box-comment_content">
                    <img src="${avatar}" alt="${username}" class="comment-avatar">
                    <div class="post__box-comment_details">
                        <div class="post__box-comment_text">
                            <span>${username}</span>
                            <div>${text}</div>
                        </div>
                        ${imgElement}
                        <div class="post__box-comment_interaction">
                            <div class="post__box-action">
                                <span>${createdAt}</span>
                                <span class='like-comment-btn ${isLiked}'>Like</span>
                                <span class="reply-btn">Reply</span>
                                <span class='total-like'>${likeCount} <i class="fas fa-heart" style="color:red"></i></span>
                            </div>
                        </div>
                    </div>
                </div>`;

      // Nếu bình luận có bình luận con, thêm nút "Xem các bình luận"
      if (hasReplies) {
        html += `
                <div class="post__box-comment_view-replies">
                    <button type="button" class="view-replies-btn" data-parent-id="${comment.comment_id}">Xem các bình luận</button>
                </div>`;
      }

      html += '</li>';
    });
    return html;
  }

  // Show Reply Comment Box
  $(document).on('click', '.reply-btn', function () {
    const commentItem = $(this).closest('.post__box-comment_item');
    if (commentItem.find('.comment__box').length === 0) {
      const replyBoxHtml = `
        <div class='comment__box' style='display:none;'>
                    <div class='comment__box-wrapper'>
                        <textarea type='text' class='comment__input' rows='2' cols='50' style='resize: none;' placeholder='Write a reply...'></textarea>
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
                      <div class='comment__box-action'>
                            <span class='comment__media'>
                                <label class="custom-file-label">
                                  <i class="fas fa-camera-retro"></i>
                                  <input type="file" class="file-comment" accept=".jpg, .jpeg, .png" />
                                </label>
                            </span>
                            <span class='reply__submit'>
                                <i class='fas fa-paper-plane'></i>
                            </span>
                      </div>
                    </div>
          </div>`;
      // Thêm vào DOM nhưng ẩn ban đầu, sau đó hiển thị với hiệu ứng trượt
      const $replyBox = $(replyBoxHtml).appendTo(commentItem); // Thêm vào cuối commentItem
      $replyBox.slideDown(); // Thực hiện hiệu ứng trượt xuống
    }
  });

  // Chức năng hiển thị danh sách những người đã like comment
  $(document).on('click', '.total-like', function () {
    const commentId = $(this).closest('.post__box-comment_item').data('comment-id');
    $.ajax({
      url: '../ajax/home/comment-handler.php',
      method: 'POST',
      data: { fetchUsersLikeComment: true, commentId: commentId },
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
});
