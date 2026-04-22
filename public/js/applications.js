function resolveAppBasePath() {
  const pathname = window.location.pathname || "";

  const publicIndex = pathname.indexOf("/public/");
  if (publicIndex !== -1) {
    return pathname.slice(0, publicIndex + "/public".length);
  }

  const apiIndex = pathname.indexOf("/api/");
  if (apiIndex !== -1) {
    const prefix = pathname.slice(0, apiIndex);
    if (!prefix) return "";
    return prefix.endsWith("/public") ? prefix : `${prefix}/public`;
  }

  const appIndex = pathname.indexOf("/webkiemthu/");
  if (appIndex !== -1) {
    const appRoot = pathname.slice(0, appIndex + "/webkiemthu".length);
    return `${appRoot}/public`;
  }

  return "";
}

const APP_BASE_PATH_APP = resolveAppBasePath();
const API_BASE_APP = `${APP_BASE_PATH_APP}/api`;

/**
 * Hàm ứng tuyển công việc
 */
async function applyJob(jobId, cvText) {
  if (!jobId) {
    alert("Không tìm thấy thông tin công việc để ứng tuyển!");
    return;
  }

  const token = localStorage.getItem("token");
  if (!token) {
    alert("Bạn cần đăng nhập để ứng tuyển!");
    window.location.href = "dangnhap.php";
    return;
  }

  if (!cvText || cvText.trim() === "") {
    alert("Vui lòng nhập giới thiệu bản thân hoặc link CV!");
    document.getElementById("cvText")?.focus();
    return;
  }

  try {
    const res = await fetch(`${API_BASE_APP}/applications.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ job_id: jobId, cv_text: cvText }),
    });

    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (err) {
      console.error("Lỗi phản hồi server:", text);
      throw new Error(
        `Server trả về dữ liệu không hợp lệ (status ${res.status}).`,
      );
    }

    if (res.ok) {
      window.location.href = "utthanhcong.php";
    } else {
      alert(data.message || "Có lỗi xảy ra khi ứng tuyển.");
    }
  } catch (error) {
    console.error(error);
    alert("Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Trả về HTML string cho status badge dựa trên trạng thái
 */
function getStatusBadge(status) {
  const s = status ? status.toLowerCase() : "submitted";
  switch (s) {
    case "interview":
    case "mời phỏng vấn":
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800">
                  <span class="size-1.5 rounded-full bg-green-600 animate-pulse"></span>
                  Mời phỏng vấn
              </span>`;
    case "reviewed":
    case "ntd đã xem":
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                  <span class="material-symbols-outlined text-[16px]">visibility</span>
                  NTD đã xem
              </span>`;
    case "rejected":
    case "bị từ chối":
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">
                  <span class="material-symbols-outlined text-[16px]">cancel</span>
                  Bị từ chối
              </span>`;
    case "submitted":
    case "đã nộp":
    default:
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                  <span class="material-symbols-outlined text-[16px]">send</span>
                  Đã nộp
              </span>`;
  }
}

/**
 * Trả về HTML string cho CV link/text
 */
function getCvLink(cv_text) {
  if (!cv_text) return "<span class='text-gray-400'>Không có thông tin</span>";
  try {
    // Check if it's a valid URL
    new URL(cv_text);
    const filename = cv_text.split("/").pop();
    return `<a class="text-primary hover:underline font-medium inline-flex items-center gap-1" href="${escapeHtml(cv_text)}" target="_blank" rel="noopener noreferrer">
              ${escapeHtml(filename || "Link CV")}
              <span class="material-symbols-outlined text-[14px]">open_in_new</span>
          </a>`;
  } catch (_) {
    // If not a URL, treat as text
    const displayText =
      cv_text.length > 30 ? cv_text.substring(0, 30) + "..." : cv_text;
    return `<span class="font-medium text-gray-700 dark:text-gray-300" title="${escapeHtml(cv_text)}">${escapeHtml(displayText)}</span>`;
  }
}

/**
 * Format date string
 */
function formatDate(dateString) {
  if (!dateString) return "Không rõ";
  const options = { day: "numeric", month: "long", year: "numeric" };
  const date = new Date(dateString);
  return isNaN(date.getTime())
    ? "Không rõ"
    : date.toLocaleDateString("vi-VN", options);
}

/**
 * Tải thông tin người dùng đang đăng nhập để hiển thị ở sidebar hồ sơ
 */
async function loadCurrentUserProfile() {
  const token = localStorage.getItem("token");
  if (!token) return;

  const nameEl = document.getElementById("profile-user-name");
  const emailEl = document.getElementById("profile-user-email");
  const avatarEl = document.getElementById("profile-user-avatar");

  if (!nameEl && !emailEl && !avatarEl) return;

  try {
    const res = await fetch(`${API_BASE_APP}/auth.php?action=me`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const text = await res.text();
    let data = {};
    try {
      data = JSON.parse(text);
    } catch (_) {
      throw new Error("Không đọc được thông tin người dùng.");
    }

    if (!res.ok) {
      throw new Error(data.message || "Tải thông tin người dùng thất bại.");
    }

    const user = data.user || {};
    const userName = (user.name || "Người tìm việc").trim();
    const userEmail = (user.email || "Không có email").trim();

    if (nameEl) nameEl.textContent = userName;
    if (emailEl) emailEl.textContent = userEmail;
    if (avatarEl) {
      avatarEl.style.backgroundImage = `url('https://ui-avatars.com/api/?name=${encodeURIComponent(
        userName,
      )}&background=066bf9&color=fff')`;
      avatarEl.setAttribute("data-alt", `Avatar ${userName}`);
    }
  } catch (err) {
    console.error("Lỗi tải thông tin người dùng:", err);
  }
}

/**
 * Tải danh sách hồ sơ ứng tuyển
 */
async function loadApplications(page = 1) {
  const token = localStorage.getItem("token");
  if (!token) return; // Chưa đăng nhập thì không tải

  const container = document.getElementById("application-list");
  if (!container) return;

  // Hiển thị loading state
  container.innerHTML = `
    <div class="col-span-full flex flex-col items-center justify-center py-16 text-secondary-text">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
      <p class="text-lg font-medium">Đang tải danh sách ứng tuyển...</p>
    </div>
  `;

  const url = new URL(
    `${API_BASE_APP}/applications.php`,
    window.location.origin,
  );
  url.searchParams.set("page", page);

  try {
    const res = await fetch(url, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (err) {
      throw new Error(
        `Server trả về dữ liệu không hợp lệ (status ${res.status}).`,
      );
    }

    if (!res.ok) {
      throw new Error(data.message || `Lỗi máy chủ: ${res.status}`);
    }
    const apps = data.applications || [];
    const pagination = data.pagination || null;

    // Cập nhật các thẻ đếm (nếu có)
    if (pagination) {
      updateStatusCounts(pagination.totalApplications, apps);
    }

    if (apps.length === 0) {
      container.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-secondary-text">
          <span class="material-symbols-outlined text-5xl mb-4">work_history</span>
          <p class="text-lg font-medium">Bạn chưa ứng tuyển công việc nào.</p>
          <p class="text-sm">Hãy bắt đầu tìm kiếm và ứng tuyển ngay hôm nay!</p>
          <a href="timkiem.php" class="mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
            Tìm việc làm
          </a>
        </div>
      `;
      renderApplicationPagination(null);
      return;
    }

    container.innerHTML = apps
      .map((app) => {
        const isJobDeleted = !app.title;
        const title = app.title || "[Công việc đã bị xóa]";
        const company = app.company || "Không có thông tin";
        const companyInitial = app.company
          ? app.company.charAt(0).toUpperCase()
          : "?";
        const detailLink = isJobDeleted ? "#" : `chitietcongviec.php?id=${app.job_id}`;
        const linkClass = isJobDeleted ? "pointer-events-none opacity-50" : "";

        return `
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 p-5 flex flex-col hover:shadow-md transition-shadow group relative ${
            isJobDeleted ? "opacity-70" : ""
          }">
              <div class="flex items-start justify-between mb-4">
                  <div class="flex gap-4 items-center min-w-0 flex-1">
                      <div class="size-14 rounded-lg bg-white border border-border-color dark:border-gray-700 flex items-center justify-center flex-shrink-0 p-1">
                           <div class="w-11 h-11 rounded bg-primary/10 flex items-center justify-center text-primary font-bold text-xl">
                              ${companyInitial}
                           </div>
                      </div>
                      <div class="overflow-hidden flex-1">
                          <a class="font-bold text-lg text-gray-900 dark:text-white leading-tight ${
                            isJobDeleted
                              ? ""
                              : "hover:text-primary transition-colors"
                          } block mb-1 truncate" href="${detailLink}" title="${escapeHtml(title)}">
                              ${escapeHtml(title)}
                          </a>
                          <p class="text-sm text-secondary-text font-medium truncate">
                              ${escapeHtml(company)}
                          </p>
                      </div>
                  </div>
              </div>
              <div class="space-y-3 mb-6 flex-1">
                  <div class="flex items-center gap-3 text-sm text-secondary-text">
                      <span class="material-symbols-outlined text-[18px] flex-shrink-0">calendar_month</span>
                      <span>Ngày ứng tuyển:
                          <span class="font-medium text-gray-700 dark:text-gray-300">
                              ${formatDate(app.created_at)}
                          </span>
                      </span>
                  </div>
                  <div class="flex items-start gap-3 text-sm text-secondary-text">
                      <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">description</span>
                      <span class="flex items-center gap-2 flex-wrap">
                          CV đã nộp: ${getCvLink(app.cv_text)}
                      </span>
                  </div>
              </div>
              <div class="pt-4 border-t border-border-color dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                  ${getStatusBadge(app.status)}
                  <div class="flex items-center gap-3">
                      <button onclick="withdrawApplication(${app.id})" class="text-sm font-medium text-gray-500 hover:text-red-600 transition-colors px-2 py-1">Rút hồ sơ</button>
                      <a href="${detailLink}" class="text-sm font-medium text-white bg-primary hover:bg-blue-700 px-4 py-2 rounded-lg transition-colors shadow-sm shadow-blue-200 dark:shadow-none ${linkClass}">
                          Xem chi tiết
                      </a>
                  </div>
              </div>
          </div>
          `;
      })
      .join("");

    renderApplicationPagination(pagination);
  } catch (e) {
    console.error("Lỗi tải danh sách ứng tuyển:", e);
    if (container) {
      container.innerHTML = `
        <div class="col-span-full text-center py-16">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
            <span class="material-symbols-outlined text-3xl text-red-600 dark:text-red-400">error</span>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Lỗi tải dữ liệu</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-4">${escapeHtml(e.message)}</p>
          <button onclick="loadApplications(1)" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
            Thử lại
          </button>
        </div>
      `;
    }
    renderApplicationPagination(null);
  }
}

/**
 * Cập nhật số lượng theo trạng thái
 */
function updateStatusCounts(totalApplications, applications) {
  // Cập nhật tổng số đã nộp
  const submittedCountEl = document.getElementById("count-submitted");
  if (submittedCountEl) {
    submittedCountEl.textContent = totalApplications;
  }

  // Đếm theo trạng thái từ data hiện tại (chỉ là ước lượng vì chỉ có data của trang hiện tại)
  if (applications && applications.length > 0) {
    const counts = {
      reviewed: 0,
      interview: 0,
      rejected: 0,
    };

    applications.forEach((app) => {
      const status = (app.status || "").toLowerCase();
      if (status === "reviewed" || status === "ntd đã xem") {
        counts.reviewed++;
      } else if (status === "interview" || status === "mời phỏng vấn") {
        counts.interview++;
      } else if (status === "rejected" || status === "bị từ chối") {
        counts.rejected++;
      }
    });

    // Cập nhật UI (chỉ là số liệu của trang hiện tại)
    const reviewedEl = document.getElementById("count-reviewed");
    const interviewEl = document.getElementById("count-interview");
    const rejectedEl = document.getElementById("count-rejected");

    if (reviewedEl) reviewedEl.textContent = counts.reviewed;
    if (interviewEl) interviewEl.textContent = counts.interview;
    if (rejectedEl) rejectedEl.textContent = counts.rejected;
  }
}

/**
 * Render phân trang cho applications
 */
function renderApplicationPagination(pagination) {
  const container = document.getElementById("pagination-container-apps");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = ""; // Không cần phân trang
    return;
  }

  const { page, totalPages, totalApplications } = pagination;
  const limit = 6; // Khớp với backend

  // Tính toán số hiển thị
  const startItem = (page - 1) * limit + 1;
  const endItem = Math.min(page * limit, totalApplications);

  let paginationHTML = `
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-secondary-text">
                Hiển thị <span class="font-medium">${startItem}</span> đến
                <span class="font-medium">${endItem}</span> trong số
                <span class="font-medium">${totalApplications}</span> kết quả
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
    `;

  // Nút Previous
  const hasPrevPage = page > 1;
  paginationHTML += `
        <a href="#" onclick="event.preventDefault(); ${hasPrevPage ? `loadApplications(${page - 1})` : "return false;"}" 
           class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasPrevPage ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Previous</span>
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>`;

  // Các nút số trang với logic thông minh
  const pageNumbers = getPageNumbers(page, totalPages);

  pageNumbers.forEach((pageNum) => {
    if (pageNum === "...") {
      paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">...</span>`;
    } else if (pageNum === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${pageNum}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); loadApplications(${pageNum})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 transition-colors">${pageNum}</a>`;
    }
  });

  // Nút Next
  const hasNextPage = page < totalPages;
  paginationHTML += `
        <a href="#" onclick="event.preventDefault(); ${hasNextPage ? `loadApplications(${page + 1})` : "return false;"}" 
           class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasNextPage ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Next</span>
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </a>`;

  paginationHTML += `
            </nav>
        </div>
    </div>
    `;

  container.innerHTML = paginationHTML;
}

/**
 * Rút hồ sơ ứng tuyển (placeholder)
 */
function withdrawApplication(appId) {
  if (confirm("Bạn có chắc chắn muốn rút hồ sơ này?")) {
    console.log("Withdraw application:", appId);
    // TODO: Implement withdraw functionality
    alert("Tính năng này đang được phát triển");
  }
}

