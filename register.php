<?php
include "config/koneksi.php";

if(isset($_POST['register'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    mysqli_query($conn,"INSERT INTO users (nama,email,password,role) 
    VALUES ('$nama','$email','$password','user')");

    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width:400px;">
        <h4 class="text-center mb-4">Daftar Akun</h4>
        <form method="POST">
            <input type="text" name="nama" class="form-control mb-3" placeholder="Nama Lengkap" required>
            <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button name="register" class="btn btn-primary w-100">Daftar</button>
        </form>
    </div>
</div>

</body>
</html>
