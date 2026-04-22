<!doctype html>
<html class="light" lang="vi">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Giao Diện Đăng Nhập Tài Khoản - DDS</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
      rel="stylesheet"
    />
    <link href="assets/css/tailwind.css" rel="stylesheet" />
    <style>
      .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
      }
      .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
      }
      .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
      }
      .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #475569;
      }
    </style>
  </head>
  <body
    class="bg-background-light dark:bg-background-dark font-display flex flex-col min-h-screen text-slate-900 dark:text-slate-100 transition-colors duration-200"
  >
    <header
      class="sticky top-0 z-50 bg-white/95 dark:bg-[#111a25]/95 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <a href="timkiem.php" class="flex items-center gap-2">
            <div
              class="flex items-center justify-center size-8 rounded bg-primary text-white"
            >
              <span class="material-symbols-outlined text-[20px]">work</span>
            </div>
            <span
              class="text-xl font-bold tracking-tight text-slate-900 dark:text-white"
              >DDS</span
            >
          </a>
          <div class="hidden md:flex flex-1 items-center justify-center gap-8">
            <a
              class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="timkiem.php"
              >Việc làm</a
            >
            <a
              class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="trangthai.php"
              >Hồ sơ &amp; CV</a
            >
            <a
              class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="#"
              >Công cụ</a
            >
            <a
              class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="#"
              >Cẩm nang</a
            >
          </div>
          <div class="flex items-center gap-3">
            <a
              class="hidden lg:block text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary mr-2"
              href="admin_dangnhap.php"
              >Admin</a
            >
            <a
              class="hidden sm:flex h-9 px-4 items-center justify-center rounded-lg text-sm font-bold text-primary dark:text-white bg-slate-50 dark:bg-slate-800 transition-colors"
              href="dangnhap.php"
            >
              Đăng nhập
            </a>
            <a
              href="dangky.php"
              class="h-9 px-4 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm shadow-blue-500/20"
            >
              Đăng ký
            </a>
          </div>
        </div>
      </div>
    </header>
    <main
      class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-background-light dark:bg-background-dark relative overflow-hidden"
    >
      <div
        class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none"
      >
        <div
          class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/10 dark:bg-blue-600/10 rounded-full blur-[100px]"
        ></div>
        <div
          class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-400/10 dark:bg-purple-600/10 rounded-full blur-[100px]"
        ></div>
      </div>
      <div
        class="w-full max-w-lg bg-white dark:bg-card-dark p-8 sm:p-10 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-black/20 border border-slate-100 dark:border-slate-800"
      >
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">
            Đăng nhập tài khoản
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Chào mừng bạn quay trở lại! Hãy đăng nhập để tiếp tục tìm kiếm cơ
            hội việc làm.
          </p>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
          <button
            class="flex items-center justify-center gap-2 h-11 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all bg-white dark:bg-[#1e293b] group"
          >
            <svg
              class="w-5 h-5"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                fill="#4285F4"
              ></path>
              <path
                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                fill="#34A853"
              ></path>
              <path
                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                fill="#FBBC05"
              ></path>
              <path
                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                fill="#EA4335"
              ></path>
            </svg>
            <span
              class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white"
              >Google</span
            >
          </button>
          <button
            class="flex items-center justify-center gap-2 h-11 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all bg-white dark:bg-[#1e293b] group"
          >
            <svg
              class="w-5 h-5"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                fill="#1877F2"
              ></path>
            </svg>
            <span
              class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white"
              >Facebook</span
            >
          </button>
        </div>
        <div class="relative mb-6">
          <div class="absolute inset-0 flex items-center">
            <div
              class="w-full border-t border-slate-200 dark:border-slate-700"
            ></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span
              class="px-2 bg-white dark:bg-card-dark text-slate-500 dark:text-slate-400"
              >Hoặc đăng nhập bằng email</span
            >
          </div>
        </div>
        <form id="loginForm" action="#" class="space-y-5" method="POST">
          <div>
            <label
              class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
              for="email"
              >Email / Tên đăng nhập</label
            >
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <span class="material-symbols-outlined text-slate-400 text-xl"
                  >mail</span
                >
              </div>
              <input
                class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-shadow"
                id="email"
                name="email"
                placeholder="Nhập email hoặc tên đăng nhập"
                required=""
                type="text"
              />
            </div>
          </div>
          <div>
            <label
              class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
              for="password"
              >Mật khẩu</label
            >
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <span class="material-symbols-outlined text-slate-400 text-xl"
                  >lock</span
                >
              </div>
              <input
                class="block w-full pl-10 pr-10 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-shadow"
                id="password"
                name="password"
                placeholder="Nhập mật khẩu của bạn"
                required=""
                type="password"
              />
              <div
                class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer hover:text-slate-600"
              >
                <span class="material-symbols-outlined text-slate-400 text-xl"
                  >visibility_off</span
                >
              </div>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded bg-slate-50 dark:bg-[#0f1723] dark:border-slate-600 cursor-pointer"
                id="remember-me"
                name="remember-me"
                type="checkbox"
              />
              <label
                class="ml-2 block text-sm text-slate-600 dark:text-slate-400"
                for="remember-me"
                >Ghi nhớ đăng nhập</label
              >
            </div>
            <div class="text-sm">
              <a
                class="font-medium text-primary hover:text-blue-600 hover:underline"
                href="#"
                >Quên mật khẩu?</a
              >
            </div>
          </div>
          <button
            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-primary hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform hover:-translate-y-0.5"
            type="submit"
          >
            Đăng nhập
          </button>
        </form>
        <div
          class="mt-8 text-center pt-6 border-t border-slate-100 dark:border-slate-700"
        >
          <p class="text-sm text-slate-600 dark:text-slate-400">
            Bạn chưa có tài khoản?
            <a
              class="font-bold text-primary hover:text-blue-500 transition-colors ml-1"
              href="dangky.php"
              >Đăng ký ngay</a
            >
          </p>
        </div>
      </div>
    </main>
    <footer
      class="bg-white dark:bg-[#111a25] border-t border-slate-200 dark:border-slate-800 pt-16 pb-8"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12"
        >
          <div>
            <div class="flex items-center gap-2 mb-6">
              <div
                class="flex items-center justify-center size-8 rounded bg-primary text-white"
              >
                <span class="material-symbols-outlined text-[20px]">work</span>
              </div>
              <span
                class="text-xl font-bold tracking-tight text-slate-900 dark:text-white"
                >DDS</span
              >
            </div>
            <p
              class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6"
            >
              Kết nối nhân tài với cơ hội. Chúng tôi giúp bạn tìm được công việc
              hoàn hảo và giúp công ty tìm được nhân viên lý tưởng.
            </p>
            <div class="flex gap-4">
              <a
                class="text-slate-400 hover:text-primary transition-colors"
                href="#"
              >
                <span class="sr-only">Facebook</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                  ></path>
                </svg>
              </a>
              <a
                class="text-slate-400 hover:text-primary transition-colors"
                href="#"
              >
                <span class="sr-only">Twitter</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                  ></path>
                </svg>
              </a>
              <a
                class="text-slate-400 hover:text-primary transition-colors"
                href="#"
              >
                <span class="sr-only">LinkedIn</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
                  ></path>
                </svg>
              </a>
            </div>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">
              Về DDS
            </h3>
            <ul class="space-y-3">
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Giới thiệu</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Tuyển dụng</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Blog</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Liên hệ</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">
              Ứng viên
            </h3>
            <ul class="space-y-3">
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Tìm việc làm</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Tạo CV</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Tra cứu lương</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Cẩm nang sự nghiệp</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">
              Nhà tuyển dụng
            </h3>
            <ul class="space-y-3">
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="quanlytindang.php"
                  >Đăng tin tuyển dụng</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Tìm hồ sơ</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Giải pháp nhân sự</a
                >
              </li>
              <li>
                <a
                  class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#"
                  >Báo giá dịch vụ</a
                >
              </li>
            </ul>
          </div>
        </div>
        <div
          class="border-t border-slate-100 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4"
        >
          <p class="text-xs text-slate-400 text-center md:text-left">
            © 2024 DDS. All rights reserved.
          </p>
          <div class="flex gap-6">
            <a
              class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
              href="#"
              >Chính sách bảo mật</a
            >
            <a
              class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
              href="#"
              >Điều khoản dịch vụ</a
            >
          </div>
        </div>
      </div>
    </footer>
    <script src="./js/auth.js?v=20260421-1"></script>
  </body>
</html>

