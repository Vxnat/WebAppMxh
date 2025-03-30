<<<<<<< HEAD
import { sendMessage, deleteMessage, getLastMessage, db } from '../config/firebase-chat.js';
import {
  collection,
  query,
  orderBy,
  onSnapshot,
} from 'https://www.gstatic.com/firebasejs/11.3.0/firebase-firestore.js';
=======
>>>>>>> e5d0414642e1bcdf00f10fe7a05ec46bf2a80d8a
$(document).ready(function () {
  // start: Sidebar
  // Mở profile để hiện chức năng đăng xuất hoặc logout
  $('.chat-sidebar-profile-toggle').on('click', function (e) {
    e.preventDefault();
    $(this).parent().toggleClass('active');
  });

  // Ẩn profile đi
  $(document).on('click', function (e) {
    if (!$(e.target).is('.chat-sidebar-profile, .chat-sidebar-profile *')) {
      $('.chat-sidebar-profile').removeClass('active');
    }
  });
  // end: Sidebar

  // start: Conversation
  // --> Chức năng nút 3 chấm hiện thao tác ở tin nhắn chat
  $(document).on('click', '.conversation-item-dropdown-toggle', function (e) {
    e.preventDefault();
    if ($(this).parent().hasClass('active')) {
      $(this).parent().removeClass('active');
    } else {
      $('.conversation-item-dropdown').removeClass('active');
      $(this).parent().addClass('active');
    }
  });

  // Ẩn nó đi
  $(document).on('click', function (e) {
    if (!$(e.target).is('.conversation-item-dropdown, .conversation-item-dropdown *')) {
      $('.conversation-item-dropdown').removeClass('active');
    }
  });
  // End: Chức năng hiện thao tác tin nhắn

  // --> Chức năng tăng chiều dài của textbox khi nhấn enter
  $(document).on('input', '.conversation-form-input', function () {
    $(this).attr('rows', $(this).val().split('\n').length);
  });

<<<<<<< HEAD
  // ******************************** Chức năng chính ********************************
=======
>>>>>>> e5d0414642e1bcdf00f10fe7a05ec46bf2a80d8a
  // Lấy dữ liệu các cuộc trò chuyện
  function getPersonChatList() {
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: { getPersonChatList: true },
      success: function (response) {
        $('.person-chat').append(response);
      },
    });
  }
  getPersonChatList();

  // Lấy các cuộc trò chuyện nhóm về
  function getGroupChatList() {
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: { getGroupChatList: true },
      success: function (response) {
        $('.group-chat').append(response);
      },
    });
  }

  getGroupChatList();

<<<<<<< HEAD
  // Lắng nghe tin nhắn realtime
  function listenForMessages(conversationId, currentUserId, isGroupChat) {
    try {
      // Kiểm tra xem có phải group chat hay không ?
      const messageRef = collection(db, !isGroupChat ? 'messages' : 'group_messages', conversationId, 'chat');
      const messagesQuery = query(messageRef, orderBy('timestamp', 'asc'));

      // Lắng nghe dữ liệu thay đổi theo thời gian thực
      return onSnapshot(messagesQuery, (querySnapshot) => {
        const messages = querySnapshot.docs.map((doc) => ({
          id: doc.id,
          ...doc.data(),
        }));

        // Render lại UI khi có tin nhắn mới
        $('.conversation-wrapper').html(renderMessages(messages, currentUserId));

        scrollToBottom(); // Cuộn xuống cuối khi có tin nhắn mới
      });
    } catch (error) {
      console.error('Lỗi khi lắng nghe tin nhắn:', error);
    }
  }

  // Hàm hiển thị tin nhắn khi có tin nhắn mới
  function renderMessages(messages, currentUserId) {
    return messages
      .map((msg) => {
        const isMe = msg.senderId == currentUserId ? 'me' : ''; // Kiểm tra tin nhắn của user hiện tại
        const avatar = msg.senderAvatar || '../img/default-avatar.png';

        return `<li class="conversation-item ${isMe}">
                  <div class="conversation-item-side">
                      <img class="conversation-item-image" src=${avatar} alt="">
                  </div>
                  <div class="conversation-item-content">
                      <div class="conversation-item-wrapper">
                          <div class="conversation-item-box">
                              <div class="conversation-item-text">
                                  <p>${msg.text}</p>
                                  <div class="conversation-item-time">${formatTimestamp(msg.timestamp)}</div>
                              </div>
                              <div class="conversation-item-dropdown">
                                    ${
                                      // Nếu là tin nhắn của bản thân thì mới hiện nút xóa
                                      isMe
                                        ? `
                                        <button type="button" class="conversation-item-dropdown-toggle"><i class="ri-more-2-line"></i></button>
                                        <ul class="conversation-item-dropdown-list">
                                          <li><a href="#" class="delete-message" data-message-id="${msg.id}"><i class="ri-delete-bin-line"></i> Delete</a></li>
                                        </ul>`
                                        : ``
                                    }
                                </div>
                          </div>
                      </div>
                  </div>
                </li>`;
      })
      .join('');
  }

  // Hàm cuộn xuống cuối tin nhắn
  function scrollToBottom() {
    $('.conversation-main').scrollTop($('.conversation-main')[0].scrollHeight);
  }

  // --> Chức năng hiện phần chat của hội thoại tương ứng
  async function generateConversationContent(response) {
    const conversationId = response.conversationId;
    const dataConversation = response.chatInfo;
    const userStatus = getUserStatus(dataConversation.lastLogin, dataConversation.isGroupChat);
    console.log(getUserStatus(dataConversation.lastLogin, dataConversation.isGroupChat));

    // Html cho khung hội thoại tin nhắn
    let html = `<div class="conversation" id="${conversationId}" data-group-chat="${dataConversation.isGroupChat}">
                    <div class="conversation-top">
                        <button type="button" class="conversation-back"><i class="ri-arrow-left-line"></i></button>
                        <div class="conversation-user">
                            <img class="conversation-user-image"
                                src="${dataConversation.avatar}"
                                alt="">
                            <div>
                                <div class="conversation-user-name">${dataConversation.name}</div>
                                <div class="conversation-user-status ${userStatus.className}">${userStatus.label}</div>
                            </div>
                        </div>
                        ${
                          dataConversation.isGroupChat
                            ? `
                            <div class="conversation-buttons">
                              <button type="button">
                                <i class="ri-information-line"></i>
                              </button>
                            </div>`
                            : ''
                        }
                    </div>
                    <div class="conversation-main">
                        <ul class="conversation-wrapper">
                        </ul>
                    </div>
                    <div class="conversation-form">
                        <button type="button" class="conversation-form-button emoji">
                          <i class="ri-emotion-line"></i>
                          <ul class="emoji-picker">
                          </ul>
                        </button>
                        <div class="conversation-form-group">
                            <textarea class="conversation-form-input" rows="1" placeholder="Type here..."></textarea>
                        </div>
                        <button type="button" class="conversation-form-button conversation-form-submit"><i class="ri-send-plane-2-line"></i></button>
                    </div>
                </div>`;

    return html;
  }

  // Load emojis from emoji.json
  function loadEmojis() {
    $.getJSON('../emoji-json/emoji.json', function (data) {
      const emojiPicker = $('.emoji-picker');
      emojiPicker.empty(); // Clear existing emojis
      data.forEach(function (emoji) {
        if (
          emoji.unicode_version.startsWith('13') ||
          emoji.unicode_version.startsWith('14') ||
          emoji.unicode_version.startsWith('15')
        ) {
          return;
        }
        emojiPicker.append(`<li>${emoji.emoji}</li>`);
      });
    });
  }

  // Toggle emoji picker
  $(document).on('click', '.conversation-form-button.emoji', function (e) {
    e.preventDefault();
    $('.emoji-picker').toggleClass('active');
  });

  // Insert emoji into the textarea
  $(document).on('click', '.emoji-picker li', function () {
    const emoji = $(this).text();
    const textarea = $('.conversation-form-input');
    textarea.val(textarea.val() + emoji);
  });

  // --> Chức năng hiện cuộc hội thoại tương ứng
  $(document).on('click', '[data-conversation]', function (e) {
    e.preventDefault(); // Ngăn chặn hành vi mặc định của thẻ a
=======
  // --> Chức năng chuyển qua trang chat khác
  // Chọn tất cả phần từ có thuộc tính data-conversation
  $(document).on('click', '[data-conversation]', function (e) {
    e.preventDefault(); // Ngăn chặn hành vi mặc định của thẻ a


>>>>>>> e5d0414642e1bcdf00f10fe7a05ec46bf2a80d8a
    // Xóa class active cho tất cả các phần tử có class .conversation
    $('.conversation').removeClass('active');

    const conversationId = $(this).data('conversation').replace(/#/g, '');
<<<<<<< HEAD
    let requestData = {
      getConversationContent: true,
      conversationId: conversationId,
    };

    // Kiểm tra nếu có data-group-chat thì mới gửi lên server
    if ($(this).data('group-chat')) {
      requestData.isGroupChat = true;
    }

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: requestData,
      success: async function (response) {
        const data = JSON.parse(response);
        // Gán hội thoại
        $('.conversation-container').html(await generateConversationContent(data));
        // Thêm class active để hiện hội thoại vừa chọn
        $(`#${conversationId}`).addClass('active'); // Thêm class active

        // Lắng nghe tin nhắn realtime
        listenForMessages(conversationId, data.chatInfo.currentUserId, data.chatInfo.isGroupChat);
      },
      complete: function () {
        // Load emojis cho conversation
        loadEmojis();
      },
      error: function () {
        alert('Lỗi khi tải cuộc trò chuyện!');
      },
    });
  });

  // -->  Chức năng gửi tin nhắn
  $(document).on('click', '.conversation-form-submit', async function () {
    const message = $('.conversation-form-input').val();
    const conversationId = $('.conversation.active').attr('id');
    const isGroupChat = $('.conversation.active').data('group-chat') === true;

    const currentUserId = $('.chat-sidebar-profile').data('user-id');
    const senderAvatar = $('.chat-sidebar-profile-toggle img').attr('src');

    if (message !== '') {
      // Xóa trắng input để cho người dùng nhập tiếp
      $('.conversation-form-input').val('');
      // Dữ liệu cho việc cập nhật tin nhắn cuối cùng
      let requestData = {
        updateLastMessage: true,
        conversationId: conversationId,
        message: message,
        senderId: currentUserId,
      };
      if (isGroupChat) {
        requestData.isGroupChat = true;
      }

      // Dữ liệu cho tin nhắn
      const data = {
        conversationId: conversationId,
        message: message,
        senderId: currentUserId,
        senderAvatar: senderAvatar,
      };

      // Gửi tin nhắn
      await sendMessage(data, isGroupChat);

      $.ajax({
        url: '../ajax/message/message-handler.php',
        method: 'POST',
        data: requestData,
        success: function (response) {
          const data = JSON.parse(response);
          if (data.success) {
            // Cập nhật last Message
            $(`.content-messages-list [data-conversation="#${conversationId}"] .content-message-text`).text(
              `${data.last_sender_name}: ${message}`,
            );
          }
        },
      });
    } else {
      alert('Bạn chưa nhập tin nhắn!');
    }
  });

  // --> Chức năng xóa tin nhắn
  $(document).on('click', '.delete-message', async function (e) {
    e.preventDefault();
    const messageId = $(this).data('message-id');
    const conversationId = $('.conversation.active').attr('id');
    const isGroupChat = $('.conversation.active').data('group-chat') === true;

    try {
      // Lấy thông tin tin nhắn cuối cùng trước khi xóa
      const lastMessageInfo = await getLastMessage(conversationId, isGroupChat);

      // Xóa tin nhắn
      await deleteMessage(messageId, conversationId, isGroupChat);

      // Kiểm tra xem tin nhắn vừa xóa có phải là tin nhắn cuối cùng không
      if (lastMessageInfo && lastMessageInfo.id === messageId) {
        const newLastMessage = await getLastMessage(conversationId, isGroupChat);

        let requestData = {
          updateLastMessage: true,
          conversationId: conversationId,
          message: newLastMessage ? newLastMessage.text : null,
          senderId: newLastMessage ? newLastMessage.senderId : null,
        };

        if (isGroupChat) {
          requestData.isGroupChat = true;
        }

        // Cập nhật tin nhắn cuối cùng
        $.ajax({
          url: '../ajax/message/message-handler.php',
          method: 'POST',
          data: requestData,
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              // Cập nhật hiển thị last message
              if (newLastMessage) {
                $(`[data-conversation="#${conversationId}"] .content-message-text`).text(
                  `${data.last_sender_name}: ${newLastMessage.text}`,
                );
              } else {
                // Nếu không còn tin nhắn
                $(`[data-conversation="#${conversationId}"] .content-message-text`).text('Chưa có tin nhắn nào');
              }
            }
          },
        });
      }
    } catch (error) {
      console.error('Lỗi khi xóa tin nhắn:', error);
    }
  });

  // Chức năng hiện thông tin chi tiết cho nhóm chat
  $(document).on('click', '.conversation-buttons', function () {
    const conversationId = $('.conversation.active').attr('id');
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        getGroupChatInfo: true,
        groupId: conversationId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        $('.dialog-container .wrapper').html(generateInfoGroupChat(data));
        $('.dialog-container').addClass('active');
      },
    });
  });

  // Giao diện CHÍNH khung thông tin nhóm chat
  function generateInfoGroupChat(data) {
    const currentUserId = data.current_user_id;
    const adminGroupId = data.group_info.admin_id;
    const isAdminGroup = currentUserId === adminGroupId;
    const avatar = data.group_info.group_avatar ? data.group_info.group_avatar : '../img/default-group.png';
    const des = data.group_info.description ? data.group_info.description : '';

    let html = `
    <div class="chat-infor">
                <button type="button" class="dialog-close"><i class="ri-close-line"></i></button>
                <div class="chat-infor-header">
                    <img class="chat-infor-header-avatar" src="${avatar}" alt="">
                    <div class="chat-infor-header-name">${data.group_info.group_name}</div>
                    <div class="chat-infor-header-description">${des}</div>

                </div>
                <div class="chat-infor-body">
                    <ul class="chat-infor-body-list">
                    ${
                      isAdminGroup
                        ? `<li class="chat-infor-body-item">
                            <button type="button" class="chat-infor-body-item-button info-group">
                                <i class="ri-group-line"></i>
                                Thông tin nhóm
                            </button>
                        </li>`
                        : ''
                    }
                        <li class="chat-infor-body-item">
                            <button type="button" class="chat-infor-body-item-button member-group">
                                <i class="ri-group-line"></i>
                                Danh sách thành viên
                            </button>
                        </li>
                        ${
                          isAdminGroup
                            ? `<li class="chat-infor-body-item">
                            <button type="button" class="chat-infor-body-item-button add-member">
                                <i class="ri-user-add-line"></i>
                                Thêm thành viên
                            </button>
                        </li>
                        <li class="chat-infor-body-item">
                            <button type="button" class="chat-infor-body-item-button remove-member">
                                <i class="ri-user-unfollow-line"></i>
                                Xóa thành viên
                            </button>
                        </li>
                        `
                            : ''
                        }
                    </ul>
                </div>
            </div>`;

    return html;
  }

  // Chức năng hiển thị thông tin nhóm chat
  $(document).on('click', '.info-group', function () {
    const conversationId = $('.conversation.active').attr('id');

    // Lưu giao diện hiện tại vào thuộc tính data
    savePreviousContent();

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        getGroupChatInfo: true,
        groupId: conversationId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        $('.dialog-container .wrapper').html(generateDetailInfoGroupChat(data));
      },
    });
  });

  // Giao diện chi tiết thông tin nhóm
  function generateDetailInfoGroupChat(data) {
    const avatar = data.group_avatar ? data.group_avatar : '../img/default-group.png';

    let html = `
    <div class="group-info">
        <button type="button" class="dialog-back"><i class="ri-arrow-left-s-line"></i></button>
        <button type="button" class="delete-group" data-group-id="${
          data.group_id
        }"><i class="ri-delete-bin-line"></i></button>
        <div class="group-info-header">
        <div class="group-info-header-title">Thông tin nhóm</div>
            <div class="group-info-header-avatar"><img src="${avatar}" alt="">
              <input type="file" accept="image/jpeg, image/png, image/jpg" class="group-info-header-avatar-edit" style="display: none" id="group-info-header-avatar-edit">
              <label class="group-info-header-avatar-label" for="group-info-header-avatar-edit"><i class="ri-pencil-line"></i></label>
            </div>
            <input type="text" class="group-info-header-name-input" placeholder="Tên nhóm" value="${data.group_name}">
            <input type="text" class="group-info-header-description-input" placeholder="Mô tả" value="${
              data.description !== null ? data.description : ''
            }">
            <button type="button" class="group-info-header-save">Lưu</button>
        </div>
    </div>`;

    return html;
  }

  // Chức năng chọn avatar cho nhóm chat
  $(document).on('change', '#group-info-header-avatar-edit', function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (event) {
        // Update the src of the img element with the file data
        $('.group-info-header-avatar img').attr('src', event.target.result);
      };
      reader.readAsDataURL(file);
    }
  });

  // Chức năng xác nhận thay đổi thông tin nhóm
  $(document).on('click', '.group-info-header-save', async function () {
    const conversationId = $('.conversation.active').attr('id');
    const newAvatar = $('.group-info-header-avatar-edit')[0];
    const newName = $('.group-info-header-name-input').val();
    const newDescription = $('.group-info-header-description-input').val();

    // Kiểm tra xem đã chọn file avatar mới chưa
    let fileAvatar = newAvatar.files.length > 0;

    if (newName === '') {
      alert('Vui lòng nhập tên nhóm');
      return;
    }

    let requestData = {
      updateGroupChatInfo: true,
      groupId: conversationId,
      newName: newName,
      newDescription: newDescription,
    };

    // Nếu đã chọn avatar mới cho nhóm
    if (fileAvatar) {
      try {
        $('.group-info').addClass('upload');
        const avatarUrl = await uploadToCloudinary(newAvatar.files[0]);
        requestData.newAvatar = avatarUrl;
        $.ajax({
          url: '../ajax/message/message-handler.php',
          method: 'POST',
          data: requestData,
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              alert('Thay đổi thông tin nhóm thành công !');
            } else {
              alert('Thay đổi thông tin nhóm thất bại !');
            }
          },
          complete: function () {
            window.location.reload();
          },
        });
      } catch (error) {
        alert('Error uploading media.');
      }
      return;
    }

    // Nếu chỉ thay đổi thông tin dạng text
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: requestData,
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert('Thay đổi thông tin nhóm thành công !');
        } else {
          alert('Thay đổi thông tin nhóm thất bại !');
        }
      },
      complete: function () {
        window.location.reload();
      },
    });
  });

  // --> Chức năng hiện danh sách thành viên nhóm chat
  $(document).on('click', '.member-group', function () {
    const conversationId = $('.conversation.active').attr('id');

    // Lưu giao diện hiện tại vào thuộc tính data
    savePreviousContent();

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        getMemberGroup: true,
        groupId: conversationId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        $('.dialog-container .wrapper').html(generateMemberGroup(data));
      },
    });
  });

  // --> Giao diện danh sách thành viên nhóm chat
  function generateMemberGroup(data) {
    let html = `
    <div class="member-group-header">
    <button type="button" class="dialog-back"><i class="ri-arrow-left-s-line"></i></button>
    <h4 class="member-group-header-title">Danh sách thành viên</h4>
    <ul class="member-group-list">`;
    data.forEach((member) => {
      const avatar = member.avatar ? member.avatar : '../img/default-avatar.png';
      html += `<li class="member-group-item">
        <img class="member-group-item-avatar" src="${avatar}" alt="">
        <div class="member-group-item-name">${member.full_name}</div>
      </li>`;
    });
    html += `</ul></div>`;
    return html;
  }

  // Chức năng thêm thành viên nhóm chat
  $(document).on('click', '.add-member', function () {
    const conversationId = $('.conversation.active').attr('id');

    // Lưu giao diện hiện tại vào thuộc tính data
    savePreviousContent();

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        addMemberGroup: true,
        groupId: conversationId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        $('.dialog-container .wrapper').html(generateListAddMember(data));
      },
    });
  });

  // --> Giao diện danh sách bạn để thêm vào nhóm chat
  function generateListAddMember(data) {
    let html = `
    <div class="member-group-header">
    <button type="button" class="dialog-back"><i class="ri-arrow-left-s-line"></i></button>
    <h4 class="member-group-header-title">Danh sách bạn</h4>
    <ul class="member-group-list">`;
    data.forEach((member) => {
      const avatar = member.avatar ? member.avatar : '../img/default-avatar.png';
      html += `<li class="member-group-item">
        <img class="member-group-item-avatar" src="${avatar}" alt="">
        <div class="member-group-item-name">${member.full_name}</div>
        <button type="button" class="add-member-btn" data-user-id="${member.user_id}"><i class="ri-user-add-line"></i></button>
      </li>`;
    });
    html += `</ul></div>`;
    return html;
  }

  // Chức năng thêm thành viên vào nhóm chat -> NHẤN XÁC NHẬN
  $(document).on('click', '.add-member-btn', function () {
    const member = $(this).closest('.member-group-item');
    const conversationId = $('.conversation.active').attr('id');
    const userId = $(this).data('user-id');

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        addMemberToGroup: true,
        groupId: conversationId,
        userId: userId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert('Thêm thành viên vào nhóm thành công!');
        } else {
          alert('Thêm thành viên vào nhóm thất bại!');
        }
      },
      complete: function () {
        member.remove();
      },
    });
  });

  // Chức năng xóa thành viên khỏi nhóm chat
  $(document).on('click', '.remove-member', function () {
    const conversationId = $('.conversation.active').attr('id');

    // Lưu giao diện hiện tại vào thuộc tính data
    savePreviousContent();

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        removeMemberGroup: true,
        groupId: conversationId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        $('.dialog-container .wrapper').html(generateListRemoveMember(data));
      },
    });
  });

  // --> Giao diện danh sách bạn để thêm vào nhóm chat
  function generateListRemoveMember(data) {
    let html = `
    <div class="member-group-header">
    <button type="button" class="dialog-back"><i class="ri-arrow-left-s-line"></i></button>
    <h4 class="member-group-header-title">Danh sách thành viên</h4>
    <ul class="member-group-list">`;
    data.forEach((member) => {
      const avatar = member.avatar ? member.avatar : '../img/default-avatar.png';
      html += `<li class="member-group-item">
        <img class="member-group-item-avatar" src="${avatar}" alt="">
        <div class="member-group-item-name">${member.full_name}</div>
        <button type="button" class="remove-member-btn" data-user-id="${member.user_id}"><i class="ri-user-unfollow-line"></i></button>
      </li>`;
    });
    html += `</ul></div>`;
    return html;
  }

  // Chức năng xóa thành viên khỏi nhóm chat
  $(document).on('click', '.remove-member-btn', function () {
    const member = $(this).closest('.member-group-item');
    const conversationId = $('.conversation.active').attr('id');
    const userId = $(this).data('user-id');

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        removeMemberToGroup: true,
        groupId: conversationId,
        userId: userId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert('Xóa thành viên vào nhóm thành công!');
        } else {
          alert('Xóa thành viên vào nhóm thất bại!');
        }
      },
      complete: function () {
        member.remove();
      },
    });
  });

  // Chức năng thêm nhóm chat
  $(document).on('click', '.add-group', function () {
    let html = `
    <div class="group-info">
        <button type="button" class="dialog-close"><i class="ri-close-line"></i></button>
        <div class="group-info-header">
        <div class="group-info-header-title">Thông tin nhóm</div>
            <div class="group-info-header-avatar"><img src="../img/default-group.png" alt="">
              <input type="file" accept="image/jpeg, image/png, image/jpg" class="group-info-header-avatar-edit" style="display: none" id="group-info-header-avatar-edit">
              <label class="group-info-header-avatar-label" for="group-info-header-avatar-edit"><i class="ri-pencil-line"></i></label>
            </div>
            <input type="text" class="group-info-header-name-input" placeholder="Tên nhóm">
            <input type="text" class="group-info-header-description-input" placeholder="Mô tả">
            <button type="button" class="group-info-header-create">Tạo nhóm</button>
        </div>
    </div>`;

    $('.dialog-container .wrapper').html(html);
    $('.dialog-container').addClass('active');
  });

  // Chức năng thêm nhóm chat -> NHẤN XÁC NHẬN
  $(document).on('click', '.group-info-header-create', async function () {
    const newAvatar = $('.group-info-header-avatar-edit')[0];
    const newName = $('.group-info-header-name-input').val();
    const newDescription = $('.group-info-header-description-input').val();

    // Kiểm tra xem đã chọn file avatar mới chưa
    let fileAvatar = newAvatar.files.length > 0;

    if (newName === '') {
      alert('Vui lòng nhập tên nhóm');
      return;
    }

    let requestData = {
      createGroupChat: true,
      newName: newName,
      newDescription: newDescription,
    };

    // Nếu đã chọn avatar mới cho nhóm
    if (fileAvatar) {
      try {
        $('.group-info').addClass('upload');
        const avatarUrl = await uploadToCloudinary(newAvatar.files[0]);
        requestData.newAvatar = avatarUrl;
        $.ajax({
          url: '../ajax/message/message-handler.php',
          method: 'POST',
          data: requestData,
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              alert('Tạo nhóm chat thành công !');
            } else {
              alert('Tạo nhóm chat thất bại !');
            }
          },
          complete: function () {
            window.location.reload();
          },
        });
      } catch (error) {
        alert('Error uploading media.');
      }
      return;
    }

    // Nếu chỉ nhập thông tin dạng text
    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: requestData,
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert('Tạo nhóm chat thành công !');
        } else {
          alert('Tạo nhóm chat thất bại !');
        }
      },
      complete: function () {
        window.location.reload();
      },
    });
  });

  // Chức năng xóa nhóm chat
  $(document).on('click', '.delete-group', function () {
    const groupId = $(this).data('group-id');
    let result = confirm('Bạn có chắc chắn muốn xóa nhóm chat này ?');

    if (!result) {
      return;
    }

    $.ajax({
      url: '../ajax/message/message-handler.php',
      method: 'POST',
      data: {
        deleteGroupChat: true,
        groupId: groupId,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          alert('Xóa nhóm chat thành công !');
        } else {
          alert('Xóa nhóm chat thất bại !');
        }
      },
      complete: function () {
        window.location.reload();
      },
    });
  });

  // Chức năng tìm kiếm người dùng hoặc nhóm chat
  $(document).on('keyup', '.content-sidebar-input', function () {
    const searchInput = $(this).val().toLowerCase();
    const members = $('.content-messages-list li a[data-conversation]');
    if (searchInput.length === 0) {
      members.show();
    }

    members.each(function () {
      const memberName = $(this).find('.content-message-name').text().toLowerCase();
      if (memberName.includes(searchInput)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // Khi thanh search rỗng sau khi nhấn "x" -> Hiện tất cả cuộc trò chuyện
  $(document).on('input', '.content-sidebar-input', function () {
    if ($(this).val() === '') {
      $('.content-messages-list li a[data-conversation]').show();
    }
  });

  // Chức năng đóng khung thông tin nhóm chat
  $(document).on('click', '.dialog-close', function () {
    $('.dialog-container').removeClass('active');
  });

  // --> Chức năng quay lại giao diện trước đó
  $(document).on('click', '.dialog-back', function () {
    const previousContent = $('.dialog-container').attr('data-previous-content');

    if (previousContent) {
      $('.dialog-container .wrapper').html(previousContent);
    }
  });

  // --> Chức năng lưu giao diện hiện tại vào thuộc tính data
  function savePreviousContent() {
    const previousContent = $('.dialog-container .wrapper').html();
    $('.dialog-container').attr('data-previous-content', previousContent);
  }

  // --> Chức năng hiện trạng thái người dùng
  function getUserStatus(lastLoginStr, isGroupChat) {
    const lastLogin = new Date(lastLoginStr).getTime();
    const now = Date.now();
    const diffMinutes = (now - lastLogin) / (1000 * 60); // Chuyển mili giây sang phút

    if (isGroupChat) {
      return { status: 'online', label: 'Group', className: 'online' };
    }

    if (diffMinutes < 5) {
      return { status: 'online', label: 'Đang hoạt động', className: 'online' };
    } else if (diffMinutes < 30) {
      return {
        status: 'recently',
        label: `Hoạt động ${Math.round(diffMinutes)} phút trước`,
        className: 'recently-active',
      };
    } else {
      return { status: 'offline', label: 'Ngoại tuyến', className: 'offline' };
    }
  }

  // Hàm định dạng thời gian cho tin nhắn
  function formatTimestamp(timestamp) {
    if (!timestamp) return '';

    const date = new Date(timestamp); // Chuyển timestamp thành Date JS
    const now = new Date();
    const diff = now - date; // Tính khoảng cách thời gian
    const oneDay = 24 * 60 * 60 * 1000;

    const optionsTime = { hour: '2-digit', minute: '2-digit' };
    const optionsDate = { day: '2-digit', month: '2-digit', year: 'numeric' };

    // Nếu là hôm nay
    if (date.toDateString() === now.toDateString()) {
      return date.toLocaleTimeString('vi-VN', optionsTime);
    }

    // Nếu là hôm qua
    if (diff < 2 * oneDay && now.getDate() - date.getDate() === 1) {
      return `Hôm qua ${date.toLocaleTimeString('vi-VN', optionsTime)}`;
    }

    // Nếu trong tuần
    const daysOfWeek = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
    if (diff < 7 * oneDay) {
      return `${daysOfWeek[date.getDay()]} ${date.toLocaleTimeString('vi-VN', optionsTime)}`;
    }

    // Nếu trước đó
    return date.toLocaleDateString('vi-VN', optionsDate) + ' ' + date.toLocaleTimeString('vi-VN', optionsTime);
  }
=======

    $(`#${conversationId}`).addClass('active'); // Thêm class active
    
  });
>>>>>>> e5d0414642e1bcdf00f10fe7a05ec46bf2a80d8a
});
