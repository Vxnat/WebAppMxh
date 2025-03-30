<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Reset Password</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #2da0a8;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        padding: 20px;
        text-align: center;
    }

    .form-container h1 {
        margin: 0 0 20px;
        font-size: 24px;
        color: #333;
    }

    .form-container p {
        font-size: 14px;
        color: #555;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .form-container input[type='email'] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-container button {
        width: 100%;
        background-color: #2da0a8;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .form-container button:hover {
        background-color: #3accd6;
    }

    .form-container .input-group {
        display: flex;
        align-items: center;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 5px 10px;
        margin-bottom: 10px;
    }

    .form-container .input-group svg {
        margin-right: 10px;
        color: #555;
    }

    .form-container .input-group input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 14px;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Reset Password</h1>
        <p>
        </p>
        <form method="post">
            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 96 960 960" width="20" fill="#555">
                    <path
                        d="M480 633q72 0 123.5-51.5T655 458q0-72-51.5-123.5T480 283q-72 0-123.5 51.5T305 458q0 72 51.5 123.5T480 633Zm0 362q-105 0-197.5-50T137 797q-11-14-15-29t-4-31q0-29 18-53t49-30q38-7 68 11t48 47q21 32 54 50.5T480 743q42 0 75-18.5t54-50.5q20-32 50-50t67-11q31 6 49.5 30t18.5 53q0 16-4.5 31T823 797q-77 96-169.5 146T480 995Z" />
                </svg>
                <input type="text" placeholder="Password" id="reset-pw" />
            </div>
            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 96 960 960" width="20" fill="#555">
                    <path
                        d="M480 633q72 0 123.5-51.5T655 458q0-72-51.5-123.5T480 283q-72 0-123.5 51.5T305 458q0 72 51.5 123.5T480 633Zm0 362q-105 0-197.5-50T137 797q-11-14-15-29t-4-31q0-29 18-53t49-30q38-7 68 11t48 47q21 32 54 50.5T480 743q42 0 75-18.5t54-50.5q20-32 50-50t67-11q31 6 49.5 30t18.5 53q0 16-4.5 31T823 797q-77 96-169.5 146T480 995Z" />
                </svg>
                <input type="text" placeholder="Confirm Password" id="cf-reset-pw" />
            </div>
            <button type="button" id="update-pw-btn">Update Password</button>
        </form>
    </div>
</body>
<script src="../js/index.js"></script>

</html>