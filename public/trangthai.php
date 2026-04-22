<?php
// Nơi để thêm mã PHP xử lý logic, ví dụ: kiểm tra đăng nhập phía server
?>
<!doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Danh Sách CV Đã Nộp - JobSite</title>
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

  <body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display text-[#0d131c]">
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
              <h2 class="text-xl font-bold tracking-tight group-hover:text-primary transition-colors">
                DDS
              </h2>
            </a>
            <div class="hidden md:flex items-center">
              <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-text">
                  <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input
                  class="block w-full pl-10 pr-3 py-2 border-none rounded-lg bg-background-light dark:bg-gray-800 text-sm focus:ring-2 focus:ring-primary placeholder-secondary-text transition-all"
                  placeholder="Tìm việc làm, công ty..." type="text" />
              </div>
            </div>
          </div>
          <div id="auth-buttons" class="flex items-center gap-6">
            <nav class="hidden lg:flex gap-8">
              <a class="text-sm font-medium hover:text-primary transition-colors" href="timkiem.php">Việc làm</a>
              <a class="text-sm font-medium hover:text-primary transition-colors" href="trangthai.php">Hồ sơ &amp;
                CV</a>
              <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Công ty</a>
              <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Cẩm nang</a>
            </nav>
            <div class="flex items-center gap-3 pl-4 border-l border-border-color dark:border-gray-700">
              <button
                class="p-2 rounded-full hover:bg-background-light dark:hover:bg-gray-800 text-secondary-text hover:text-primary transition-colors relative">
                <span class="material-symbols-outlined">notifications</span>
                <span
                  class="absolute top-1.5 right-1.5 size-2 bg-red-500 rounded-full border-2 border-white dark:border-gray-900"></span>
              </button>
              <button
                class="p-2 rounded-full hover:bg-background-light dark:hover:bg-gray-800 text-secondary-text hover:text-primary transition-colors">
                <span class="material-symbols-outlined">chat_bubble</span>
              </button>
              <div class="relative ml-2">
                <a href="dangnhap.php"
                  class="h-9 px-4 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm">
                  Đăng nhập
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
      <aside class="w-full md:w-64 flex-shrink-0">
        <div
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 overflow-hidden sticky top-24">
          <div
            class="p-6 border-b border-border-color dark:border-gray-700 bg-gradient-to-br from-primary/5 to-transparent">
            <div class="flex items-center gap-4">
              <div id="profile-user-avatar" class="size-12 rounded-full bg-cover bg-center border-2 border-white shadow-sm"
                data-alt="User profile picture" style="
                  background-image: url('https://ui-avatars.com/api/?name=User&background=066bf9&color=fff');
                "></div>
              <div class="flex flex-col overflow-hidden">
                <h3 id="profile-user-name" class="font-bold text-base truncate">Người tìm việc</h3>
                <p id="profile-user-email" class="text-secondary-text text-xs truncate">
                  Vui lòng đăng nhập
                </p>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-secondary-text">
              <span>Đã hoàn thiện hồ sơ</span>
              <span class="font-bold text-primary">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
              <div class="bg-primary h-1.5 rounded-full" style="width: 0%"></div>
            </div>
          </div>
          <nav class="p-3 space-y-1">
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary-text hover:bg-background-light dark:hover:bg-gray-700 hover:text-primary transition-colors group"
              href="#">
              <span class="material-symbols-outlined group-hover:scale-110 transition-transform">dashboard</span>
              <span class="text-sm font-medium">Bảng điều khiển</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary transition-colors"
              href="#">
              <span class="material-symbols-outlined fill">work</span>
              <span class="text-sm font-bold">Việc làm đã nộp</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary-text hover:bg-background-light dark:hover:bg-gray-700 hover:text-primary transition-colors group"
              href="#">
              <span class="material-symbols-outlined group-hover:scale-110 transition-transform">favorite</span>
              <span class="text-sm font-medium">Việc làm đã lưu</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary-text hover:bg-background-light dark:hover:bg-gray-700 hover:text-primary transition-colors group"
              href="#">
              <span
                class="material-symbols-outlined group-hover:scale-110 transition-transform">notifications_active</span>
              <span class="text-sm font-medium">Thông báo việc làm</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary-text hover:bg-background-light dark:hover:bg-gray-700 hover:text-primary transition-colors group"
              href="#">
              <span class="material-symbols-outlined group-hover:scale-110 transition-transform">description</span>
              <span class="text-sm font-medium">Quản lý CV</span>
            </a>
            <div class="h-px bg-border-color dark:bg-gray-700 my-2 mx-3"></div>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary-text hover:bg-background-light dark:hover:bg-gray-700 hover:text-red-500 transition-colors group"
              href="dangnhap.php">
              <span class="material-symbols-outlined group-hover:scale-110 transition-transform">logout</span>
              <span class="text-sm font-medium">Đăng xuất</span>
            </a>
          </nav>
        </div>
      </aside>
      <main class="flex-1 min-w-0">
        <nav aria-label="Breadcrumb" class="flex mb-6">
          <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm">
            <li>
              <a class="text-secondary-text hover:text-primary transition-colors" href="timkiem.php">Trang chủ</a>
            </li>
            <li><span class="text-gray-300">/</span></li>
            <li>
              <a class="text-secondary-text hover:text-primary transition-colors" href="#">Tài khoản</a>
            </li>
            <li><span class="text-gray-300">/</span></li>
            <li class="font-medium text-gray-900 dark:text-white">
              Việc làm đã nộp
            </li>
          </ol>
        </nav>
        <div class="mb-8">
          <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">
            Việc làm đã nộp
          </h1>
          <p class="text-secondary-text">
            Theo dõi trạng thái các hồ sơ bạn đã ứng tuyển và cơ hội nghề nghiệp
            của bạn.
          </p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-border-color dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-secondary-text">Đã nộp</p>
              <span class="p-1.5 bg-blue-50 text-blue-600 rounded-md">
                <span class="material-symbols-outlined text-[20px]">send</span>
              </span>
            </div>
            <p id="count-submitted" class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
          </div>
          <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-border-color dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-secondary-text">NTD đã xem</p>
              <span class="p-1.5 bg-purple-50 text-purple-600 rounded-md">
                <span class="material-symbols-outlined text-[20px]">visibility</span>
              </span>
            </div>
            <p id="count-reviewed" class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
          </div>
          <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-border-color dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-secondary-text">Phỏng vấn</p>
              <span class="p-1.5 bg-green-50 text-green-600 rounded-md">
                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
              </span>
            </div>
            <p id="count-interview" class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
          </div>
          <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-border-color dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-secondary-text">Bị từ chối</p>
              <span class="p-1.5 bg-red-50 text-red-600 rounded-md">
                <span class="material-symbols-outlined text-[20px]">cancel</span>
              </span>
            </div>
            <p id="count-rejected" class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
          </div>
        </div>
        <div
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 p-4 mb-6">
          <div class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-text">
                <span class="material-symbols-outlined text-[20px]">search</span>
              </div>
              <input
                class="block w-full pl-10 pr-3 py-2.5 border border-border-color dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary placeholder-secondary-text"
                placeholder="Tìm kiếm theo tên công việc hoặc công ty..." type="text" />
            </div>
            <div class="flex gap-4">
              <div class="relative min-w-[160px]">
                <select
                  class="block w-full pl-3 pr-10 py-2.5 text-sm border border-border-color dark:border-gray-600 rounded-lg focus:ring-primary focus:border-primary bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 appearance-none">
                  <option>Tất cả trạng thái</option>
                  <option>Đã nộp</option>
                  <option>NTD đã xem</option>
                  <option>Được mời phỏng vấn</option>
                  <option>Bị từ chối</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-secondary-text">
                  <span class="material-symbols-outlined text-[20px]">expand_more</span>
                </div>
              </div>
              <div class="relative min-w-[160px]">
                <select
                  class="block w-full pl-3 pr-10 py-2.5 text-sm border border-border-color dark:border-gray-600 rounded-lg focus:ring-primary focus:border-primary bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 appearance-none">
                  <option>30 ngày qua</option>
                  <option>60 ngày qua</option>
                  <option>Năm nay</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-secondary-text">
                  <span class="material-symbols-outlined text-[20px]">expand_more</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="application-list" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
          <!-- Application list will be loaded here by JavaScript -->
          <div class="col-span-full flex flex-col items-center justify-center py-16 text-secondary-text">
            <span class="material-symbols-outlined text-5xl mb-4 animate-spin">progress_activity</span>
            <p class="text-lg font-medium">Đang tải danh sách ứng tuyển...</p>
          </div>
        </div>
        <div id="pagination-container-apps"
          class="mt-6 flex items-center justify-between border-t border-border-color dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl shadow-sm px-4 py-3 sm:px-6 min-h-[64px]">
          <!-- Pagination for applications will be rendered here -->
        </div>
      </main>
    </div>
    <script src="./js/utils.js?v=20260421-1"></script>
    <script src="./js/auth.js?v=20260421-1"></script>
    <script src="./js/applications.js?v=20260421-1"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
      const token = localStorage.getItem("token");
      if (!token) {
        alert("Vui lòng đăng nhập để xem hồ sơ.");
        window.location.href = "dangnhap.php";
        return;
      }
      if (typeof loadCurrentUserProfile === "function") {
        loadCurrentUserProfile();
      }
      loadApplications(1);
    });
    </script>
  </body>

</html>

