document.addEventListener("DOMContentLoaded", function () {
    // Lấy tất cả các phần tử savedpost và gán thuộc tính data-id
    document.querySelectorAll(".savedpost").forEach(post => {
        const postId = post.getAttribute("data-id");
        if (!postId) {
            console.error("Bài viết không có ID");
        }
    });

    // Lắng nghe sự kiện click trên tất cả các nút xóa
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
            const postElement = this.closest(".savedpost");
            const postId = postElement.getAttribute("data-id");
            
            if (!postId) {
                console.error("Không tìm thấy ID bài viết để xoá");
                return;
            }
            
            if (confirm("Bạn có chắc chắn muốn xoá bài viết này không?")) {
                fetch("delete.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: postId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        postElement.remove(); // Xóa khỏi giao diện
                    } else {
                        alert("Xoá thất bại: " + data.message);
                    }
                })
                .catch(error => console.error("Lỗi khi gửi yêu cầu xoá:", error));
            }
        });
    });
});
