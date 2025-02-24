<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết nối bạn bè</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- CSS -->
    <link rel="stylesheet" href="../css/suggest.css">
    <!-- iCON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="bard-sidenav-container">
        <div class="bard-sidenav">
            <div class="header">
                <a href="#">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="header-title">
                    <a href="#">
                        <p>Bạn bè</p>
                    </a>
                    <h2>Gợi ý kết bạn</h2>
                </div>
            </div>

            <div class="suggestion-title">
                <p>Những người bạn có thể biết</p>
            </div>
            <!--Hiển thị danh sách gợi ý kết bạn-->
            <ul class="suggestions-list">
                <!-- <li class="suggestion">
                    <a href="#"><img src="../img/default-avatar.png" alt=""></a>
                    <div class="suggestion-details">
                        <h4>PhamXuanNhat</h4>
                        <div class="common-friends">
                            <a href="#"><img src="../img/default-avatar.png" alt=""></a>
                            <p>10 bạn chung</p>
                        </div>
                        <div class="action-buttons">
                            <a href="#"><button class="add">Thêm bạn bè</button></a>
                            <a href="#"><button class="delete">Gỡ</button></a>
                        </div>
                    </div>
                </li> -->
            </ul>

        </div>

        <div class="bard-sidenav-content">
            <div class="content-container">
                <h2>Lời mời kết bạn</h2>
                <!-- <p class="no-request">Chưa có lời mời kết bạn</p> -->
                <!-- Hiển thị danh sách gợi ý kết bạn -->
                <ul class="friend-request-list">
                    <li class="friend-request">
                        <div class="friend-request-info">
                            <div class="friend-request-avatar">
                                <a href="#">
                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADACAMAAAB/Pny7AAAAY1BMVEXZ3OFwd39yd3vc3+Rxd31weHtydn/Z3eBweH3f4ufV2N1vdHhueHrIy9Dc4ONpbnJqcXqOk5hncHK+wsaZnaHO0tezt7t5gIOoq7B+hIyFio6hpamusbZ6foRka3O6vcPJ1NXEzo3dAAAGeUlEQVR4nO2dW5ubKhRADVuNgMQbiqKO/f+/8mAybZOZNCYKQuawHtp+bR9Y5bI3F3eDwOPxeDwej8fj8Xg8Ho/H4/F4PB6Px+PxeDwej+f/CXxiux2bUQqkrmvG1A8keGchJcKmtugOaVnSsCvaiSkh261aBQSs4eNHnueHM+oXHyNv2BvqAFS9pJ8ef8mo7Kt308G1UqEo/CqDEFU6NbbdvhcA3HSHQxh+c7n8Xtg1+G06BxP+zeJWCXHyJp2DmYySBRkq2VvYQCXSdEkG0XFyf6QdTw2ljwfZBUqbo+3GLjIh+n3e3x1paLLd1gWgQncW5HuokYYqp0casJHeWZDvdk0YUsFOtlv8b4DIp+bL786hkrjbN5iX0SsyKOPOLtC4yZPHa/K3oVY2jtpAPX5LLJdIRO3mQMP8ual/IxO5OdBwNa6QScfKRRtSPBUtbwmjiBPbLf8OnsQ6GTE52DX8ydD/VYa61zVQdYfXXdSkCUPhXlYzrFE566SDYzJQFy/HmN8yZeFYrIEqWtszat44Ns6gKV9KZK5dUDbYbv4thG+RcWs9g7pb2PY/ANHOqUkDbE2Q+UPMXJIJpmyLzIdbpwHDJpl8CBw6qTn222R64pAMWZWY/ZXh5OiOjUr/N8kULskEW2Ti+IfJBF7GED9pmBG+bWnmtgVu+EkyW4Nma1vghi3pTJg4tqFpNshEUdnYbv8N1RaZtHRq36z2MwsXzA8Iw9Cp/QzU4/qdZpw7ttOsi9VnAEqmqN0JmTPtFhm3VuYAT6tlkrR07H0DMLH+eFY4Nf/Pk+aVe+YbGeeOZ9WkWXvWfEgdmzKXK42VOHilsfoagBbuvWyAIXnyock1KvqjxjkXtQTINXeaiErnpr8Ct2vuNBFqHbyfDY5HuULG1bdAeEJR9MpDoHPPuHhxPoN5+qpMXjjqEgAR9DWZvHNzkM1gFr2S1CTJweUHwXh65ZUWjVydMGeO0Dxvk48Ohstbpi4L730FcM38FxIq3Lr8u8eRFRlaCJ8qVKJMVrab+gRAepotyVDau7uO3QBMZo9lMsne5qMTwExSmqYqhl6RzFtkRVnK6m1UZpROL/9oXKTmoad+kj3DLq/I9wAcsIFLKURyVhFCdJ3kAwveqlf+ABgf62pqhlYxNFNVB/g9TT4BZXThR3xE6/F4/t8cL9huhh5+isy5RsPpwhvHmXOQPFdp+M2lWMPbKakWE8ampuVF0XXjnPaPoyx4385JDVF//C5jTv3Ts6nlUqhkvyxpHseH+elSHFKalWUkJG8bRt6hgwATpkRGSml0Tpev9mRqN52o36QlHef0mTiedOITawsR5nn8cKd5iPMciaJ1eWODcVWINErDOF6SiWOkuk4UlaM6+DSIy075ORmE5r89DuCeDgRDVK64PU9LMbg2eUgzZtGK74HmD+iysXHokxMIqqJMo5UyURqVsnLkFA2g5moyf1mGn+VyCEUpr08O1AYAMokMrdC4BqFMNPY758RUt6DtMiiNuOUnNBA0koZoxQOAW8IQJUkuJ5tVnCBox8WY8oLS2NqzAcLnAKnLZV4NuK2Jg4mK+MvR/nmSNC1lYCMhOOL6e/my7VBqo14YsGztc7lHIET3X9Sg2r4g32O+VNu7EAVUaOkWdq1NuHfZI6iEiTH2qUPHPW2gkusfmC/LzKXcdrMBVmx4+r8so0ZasZeNipVGZssVSbpb9OwNjrFPmYj2u6hAlZmXSVG2xyKAfy2+JtFD+cu8DAi6k4ww3jW4L9E+MlHZG84EcJVFhkL/V5IoN5vXzEX/TGRk9zH8ghO3ZtLLf9mYfMANTGwqYvAqodHPhLjpAHOLSgSMqcz12HaWMVbJDQJO9+4Zyg2d18Ck8yTmKeLYUP1DlSwvXYgZkMnNpM9QjRZ6xlCRPdJnFmTizMReYMNnpZugRgq6rq8ttwmUGah8QEyeYTxg/pBLuwzbVPJjCx9Mu8y2+iWbZLR/Lgz7ppg3jJpXAGAflkwUJdP7Bgr3Bq4vnpbRvH/Goz2XQyq0ykD9+IMYs0Sl1riJp9KiDMq07mowtypDtVZ1xzK1KiM1ygCJrMogpDGjAUbtylCNmxrcpJZlNP4vFZjvefR3h1xj2DwVlmUOhcZAI3Y8Yb5Lp8+FjKFlmVHbcga1XZMZbQkNMNsqB32F9qAKD1b2/3/R9wIFV+texmpE3/9Xhxv7MtqippfRzFMy/wFUEG8djal5cQAAAABJRU5ErkJggg=="
                                        alt="">
                                </a>
                            </div>
                            <a href="#">Phạm Xuân Nhật</a>
                        </div>
                        <div class="friend-request-actions">
                            <button class="accept">Đồng ý</button>
                            <button class="decline">Từ chối</button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
<script src="../js/suggest.js"></script>

</html>