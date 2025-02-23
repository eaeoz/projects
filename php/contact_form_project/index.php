<?php

$_POST['captchaAnswer'] = null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-image: url('https://picsum.photos/2000/1000');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .popup-form {
        background-color: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 500px;
        transform: scale(0);
        transition: transform 0.5s ease-in-out;
    }

    .popup-form.show {
        transform: scale(1);
    }

    .popup-form h2 {
        text-align: center;
        margin-bottom: 20px;
        background-color: #4CAF50;
        color: #fff;
        padding: 10px;
        border-radius: 5px 5px 0 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
    }

    .form-control {
        width: 100%;
        height: 40px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-control.textarea {
        min-height: 200px;
        resize: vertical;
        background-color: #f2f2f2;
        border-radius: 5px;
        padding: 10px;
    }

    .btn {
        width: 100%;
        height: 40px;
        background-color: #4CAF50;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }

    #captchaAnswer,
    #captchaLabel {
        display: inline-block;
        vertical-align: middle;
    }

    #captchaLabel {
        width: 120px;
        text-align: right;
        margin-right: 10px;
        color: #4CAF50;
        font-weight: bold;
        padding-bottom: 2px;
    }

    #captchaAnswer {
        width: 150px;
        height: 30px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .btn:hover {
        background-color: #3e8e41;
    }
    </style>
    <title>Iletisim Formu</title>
</head>

<body>

    <div class="popup-form">
        <h2>İletişim Formu</h2>
        <form action="./sonuc.php" method="post" id="contact-form">
            <div class="form-group">
                <label for="name">Ad Soyad</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="phone">Telefon Numarası</label>
                <input type="tel" class="form-control" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="email">Email Adresi</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="subject">Konu</label>
                <input type="text" class="form-control" id="subject" name="subject">
            </div>
            <div class="form-group">
                <label for="message">Mesaj</label>
                <textarea class="form-control textarea" id="message" name="message"></textarea>
            </div>
            <!-- <label for="captchaAnswer" id="captchaLabel"></label> -->
            <img id="captchaImage" src="" />
            <input type="text" class="form-control" id="captchaAnswer" name="captchaAnswer">
            <button type="submit" class="btn">Gönder</button>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.popup-form').classList.add('show');
    });
    </script>

    <!-- conirmation olmadan swal -->
    <!-- <script>
        $(document).ready(function() {
            console.log('JavaScript code is running!'); // page loads
            $('#contact-form').submit(function(e) {
                console.log('Form Submitted!'); // form submitted
                e.preventDefault(); // Prevent the default form submission behavior

                var formData = $('#contact-form').serialize();

                $.ajax({
                    type: 'POST',
                    url: $('#contact-form').attr('action'),
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Mail Gönderimi Başarılı!',
                                text: response.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Bir hata oluştu. Lütfen tekrar deneyin.',
                        });
                    }
                });
                // Clean the form
                $('#contact-form')[0].reset();
            });
        });
    </script> -->

    <!-- confirmation ile swal -->
    <script>
    $(document).ready(function() {
        // console.log('JavaScript code is running!'); // page loads

        // you can display associated text (less secure)
        // $.ajax({
        //     type: 'GET',
        //     url: './captcha.php',
        //     dataType: 'json',
        //     success: function(response) {
        //         $('#captchaLabel').html('Ans: ' + response.captchaChallenge + ' _');
        //     }
        // });

        $.ajax({
            type: 'GET',
            url: './captcha.php',
            dataType: 'json',
            success: function(response) {
                $('#captchaImage').attr('src', 'data:image/png;base64,' + response
                    .captchaChallenge);
            }
        });

        $('#contact-form').submit(function(e) {
            console.log('Form Submitted!'); // form submitted
            e.preventDefault(); // Prevent the default form submission behavior

            // Get the user's answer to the CAPTCHA challenge
            var userAnswer = $('#captchaAnswer').val();

            // Validate the user's answer
            $.ajax({
                type: 'POST',
                url: './captcha.php',
                data: {
                    captchaAnswer: userAnswer
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // If the CAPTCHA answer is correct, proceed with the form submission
                        var formData = $('#contact-form').serialize();

                        $.ajax({
                            type: 'POST',
                            url: $('#contact-form').attr('action'),
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Mail Gönderimi Başarılı!',
                                        text: response.message,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Reload the page
                                            location.reload();
                                            $('#contact-form')[0]
                                                .reset();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: response.message,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Reload the page
                                            location.reload();
                                        }
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: 'Bir hata oluştu. Lütfen tekrar deneyin.' +
                                        error,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Reload the page
                                        $('#captchaAnswer').val('');
                                        location.reload();
                                    }
                                });
                            }
                        });
                    } else {
                        // If the CAPTCHA answer is incorrect, display an error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Captcha cevabi yanlis. Lütfen tekrar deneyin.',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reload the page
                                $('#captchaAnswer').val('');
                                location.reload();
                            }
                        });
                    }
                }
            });
        });
    });
    </script>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>