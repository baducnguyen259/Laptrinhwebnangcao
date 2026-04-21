<?php
// Nơi để thêm mã PHP xử lý logic, ví dụ: lấy thông tin công việc vừa ứng tuyển
?>
<!doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Ứng tuyển thành công - DDS</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap"
      rel="stylesheet" />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
      rel="stylesheet" />
    <link href="assets/css/tailwind.css" rel="stylesheet" />
    <style>
    body {
      font-family: "Inter", sans-serif;
    }

    .material-symbols-outlined {
      font-variation-settings:
        "FILL"0,
        "wght"400,
        "GRAD"0,
        "opsz"24;
    }

    .material-symbols-outlined.fill {
      font-variation-settings: "FILL"1;
    }
    </style>
  </head>

  <body
    class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display text-[#0d131c] dark:text-gray-200">
    <header class="bg-white dark:bg-[#1a202c] border-b border-border-color dark:border-gray-800 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-8">
            <a class="flex items-center gap-2 group" href="timkiem.php">
              <div class="size-8 text-primary">
                <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M4 42.4379C4 42.4379 14.0962 36.0744 24 41.1692C35.0664 46.8624 44 42.2078 44 42.2078L44 7.01134C44 7.01134 35.068 11.6577 24.0031 5.96913C14.0971 0.876274 4 7.27094 4 7.27094L4 42.4379Z"
                    fill="currentColor"></path>
                </svg>
              </div>
              <h2
                class="text-xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-primary transition-colors">
                DDS
              </h2>
            </a>
          </div>
          <div id="auth-buttons" class="flex items-center gap-6">
            <!-- Auth buttons will be loaded by auth.js -->
            <a href="dangnhap.php"
              class="h-9 px-4 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm">
              Đăng nhập
            </a>
          </div>
        </div>
      </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="w-full max-w-md text-center">
        <div
          class="mx-auto mb-6 flex items-center justify-center size-24 rounded-full bg-green-100 dark:bg-green-900/30">
          <span class="material-symbols-outlined text-5xl text-green-600 dark:text-green-400">task_alt</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
          Ứng tuyển thành công!
        </h1>
        <p class="text-secondary-text mb-8">
          Hồ sơ của bạn đã được gửi đến nhà tuyển dụng. Bạn có thể theo dõi trạng thái ứng tuyển trong trang hồ sơ cá
          nhân.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="timkiem.php"
            class="h-11 px-6 flex items-center justify-center rounded-lg border border-border-color dark:border-gray-700 text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            Tiếp tục tìm việc
          </a>
          <a href="trangthai.php"
            class="h-11 px-6 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm">
            Xem lịch sử ứng tuyển
          </a>
        </div>
      </div>
    </main>

    <footer class="bg-white dark:bg-[#1a202c] border-t border-border-color dark:border-gray-800 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-secondary-text">
        &copy; 2026 DDS. All rights reserved.
      </div>
    </footer>

    <script src="./js/auth.js?v=20260421-1"></script>
  </body>

</html>