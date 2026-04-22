<!doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng tin tuyển dụng - DDS</title>
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
      font-variation-settings: "FILL"0, "wght"400, "GRAD"0, "opsz"24;
    }
    </style>
  </head>

  <body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display text-[#0d131c]">
    <header class="bg-white dark:bg-[#1a202c] border-b border-border-color dark:border-gray-800 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-8">
            <a class="flex items-center gap-2 group" href="timkiem.php">
              <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
                <span class="material-symbols-outlined text-[20px]">work</span>
              </div>
              <h2
                class="text-xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-primary transition-colors">
                DDS
              </h2>
            </a>
          </div>
          <div id="auth-buttons" class="flex items-center gap-3"></div>
        </div>
      </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
      <div id="post-job-section"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 p-6 mb-8">
        <div class="mb-6 border-b border-border-color dark:border-gray-700 pb-4">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Đăng tin tuyển dụng mới</h2>
              <p class="text-secondary-text mt-1">Nhập đầy đủ thông tin để hiển thị đúng trên trang chi tiết công việc.
              </p>
            </div>
            <a href="quanlytindang.php"
              class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-border-color dark:border-gray-600 text-sm font-semibold text-secondary-text hover:text-primary hover:border-primary transition-colors">
              Xem danh sách tin đã đăng
            </a>
          </div>
        </div>

        <form id="postJobForm" class="space-y-6" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tiêu đề công việc <span
                  class="text-red-500">*</span></label>
              <input type="text" name="title" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Senior Frontend Developer">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tên công ty <span
                  class="text-red-500">*</span></label>
              <input type="text" name="company" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Tech Corp">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Logo công ty</label>
              <input type="file" name="company_logo_file" accept="image/png,image/jpeg,image/webp,image/gif"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white file:mr-3 file:rounded-md file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/20">
              <p class="mt-1 text-xs text-secondary-text">Định dạng: JPG, PNG, WEBP, GIF. Tối đa 2MB.</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Địa điểm làm việc <span
                  class="text-red-500">*</span></label>
              <input type="text" name="location" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Hà Nội">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mức lương <span
                  class="text-red-500">*</span></label>
              <input type="text" name="salary" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: 20 - 30 triệu">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Ngành nghề</label>
              <input type="text" name="field"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Công nghệ thông tin">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Hình thức làm
                việc</label>
              <input type="text" name="job_type"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Toàn thời gian">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kinh nghiệm</label>
              <input type="text" name="experience"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: 3 - 5 năm">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Cấp bậc</label>
              <input type="text" name="job_level"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Senior / Trưởng nhóm">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Hạn nộp hồ sơ</label>
              <input type="date" name="deadline"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Quy mô công ty</label>
              <input type="text" name="company_size"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: 500-1000 nhân viên">
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Lĩnh vực công
                ty</label>
              <input type="text" name="company_industry"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Software & IT">
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mô tả công việc</label>
              <textarea name="description" rows="5"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Mô tả chi tiết công việc"></textarea>
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kỹ năng & công nghệ
                (dấu phẩy hoặc xuống dòng)</label>
              <textarea name="skills" rows="3"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Figma, Design System, HTML/CSS"></textarea>
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Yêu cầu học vấn & kinh
                nghiệm (mỗi dòng 1 ý)</label>
              <textarea name="education_requirements" rows="4"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Tốt nghiệp đại học chuyên ngành liên quan"></textarea>
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kỹ năng mềm & ngôn ngữ
                (mỗi dòng 1 ý)</label>
              <textarea name="soft_skills" rows="4"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Giao tiếp tốt"></textarea>
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Hồ sơ ứng viên phù
                hợp</label>
              <textarea name="candidate_profile" rows="4"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Mô tả mẫu ứng viên phù hợp"></textarea>
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Điểm cộng</label>
              <textarea name="candidate_bonus" rows="3"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Có kinh nghiệm B2B SaaS"></textarea>
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Phúc lợi (mỗi dòng theo
                mẫu: Tiêu đề|Mô tả)</label>
              <textarea name="benefits" rows="4"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Bảo hiểm sức khỏe|Gói bảo hiểm toàn diện"></textarea>
            </div>
          </div>

          <div class="pt-2 flex flex-wrap justify-end gap-3">
            <button type="reset"
              class="px-5 py-2.5 rounded-lg border border-border-color dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              Làm mới
            </button>
            <button type="submit"
              class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">
              Đăng tin ngay
            </button>
          </div>
        </form>
      </div>
    </main>

    <script src="./js/utils.js?v=20260421-1"></script>
    <script src="./js/auth.js?v=20260421-1"></script>
    <script src="./js/jobs.js?v=20260421-1"></script>
    <script>
    const postJobForm = document.getElementById("postJobForm");
    if (postJobForm) {
      postJobForm.addEventListener("submit", createJob);
    }
    </script>
  </body>

</html>

