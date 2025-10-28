<?php
require_once '../src/config/connection.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Cek jika form telah dikirim (tombol login ditekan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login berhasil, set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit();
        }
    }

    // Jika login gagal, set pesan error di session
    $_SESSION['error'] = "Username atau password yang Anda masukkan salah.";
    
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CoreTask</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="flex min-h-full bg-white justify-center">
        <div class="flex flex-col justify-center px-4 py-12 sm:px-6 lg:px-20 lg:w-1/2">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <h2 class="mt-8 text-3xl font-bold leading-9 tracking-tight text-gray-900">
                        Selamat datang kembali!
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Silakan masuk ke akun CoreTask Anda.
                    </p>
                </div>

                <div class="mt-10">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">' . htmlspecialchars($_SESSION['error']) . '</div>';

                        unset($_SESSION['error']);
                    }
                    ?>
                    <form method="POST" action="login.php" class="space-y-6">
                        <div>
                            <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username</label>
                            <div class="mt-2">
                                <input id="username" name="username" type="text" required class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-gray-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" required class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-gray-600 sm:text-sm sm:leading-6">
                                <div class="text-right">
                                    <a href="#" class="text-sm font-semibold leading-6 text-gray-900 mt-2 hover:border-b hover:border-gray-900">Lupa password?</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md bg-zinc-900 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-zinc-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Login to CoreTask →
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="relative hidden lg:block lg:w-2/5">
            <img class="absolute inset-0 h-full w-full object-contain" src="img/login-pict.jpg" alt="Ilustrasi orang bekerja">
        </div>
    </div>
</body>
</html>