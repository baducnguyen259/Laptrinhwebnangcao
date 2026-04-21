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

const JOBS_APP_BASE_PATH = resolveAppBasePath();
const API_BASE_JOBS = `${JOBS_APP_BASE_PATH}/api`;
const PUBLIC_BASE_PATH = JOBS_APP_BASE_PATH;

function decodeJwtPayload(token) {
  try {
    const base64Url = token.split(".")[1];
    if (!base64Url) return null;
    let base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    while (base64.length % 4 !== 0) {
      base64 += "=";
    }
    return JSON.parse(atob(base64));
  } catch (_) {
    return null;
  }
}

function getCurrentUserRole() {
  const token = localStorage.getItem("token");
  if (!token) return null;
  const payload = decodeJwtPayload(token);
  return payload?.data?.role || payload?.role || null;
}

function getJobDetailUrl(jobId) {
  return `${PUBLIC_BASE_PATH}/chitietcongviec.php?id=${encodeURIComponent(jobId)}`;
}

function getJobApplyUrl(jobId) {
  return `${getJobDetailUrl(jobId)}#apply-section`;
}

// Đảm bảo hàm formatSalary tồn tại (phòng trường hợp utils.js bị cache cũ hoặc chưa tải)
if (typeof formatSalary === "undefined") {
  window.formatSalary = function (salary) {
    if (!salary) return "Thỏa thuận";
    if (!isNaN(salary) && !isNaN(parseFloat(salary))) {
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      })
        .format(salary)
        .replace("₫", "VNĐ");
    }
    return salary;
  };
}

if (typeof escapeHtml === "undefined") {
  window.escapeHtml = function (text) {
    return String(text ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  };
}

function getCompanyInitial(companyName) {
  const words = String(companyName || "")
    .trim()
    .split(/\s+/)
    .filter(Boolean);

  if (words.length >= 2) {
    return `${words[0][0] || ""}${words[1][0] || ""}`.toUpperCase();
  }
  if (words.length === 1) {
    return (words[0][0] || "C").toUpperCase();
  }
  return "C";
}

function normalizeCompanyLogoUrl(rawUrl) {
  const value = String(rawUrl || "").trim();
  if (!value) return "";

  if (/^https?:\/\//i.test(value)) return value;
  if (value.startsWith("/")) return value;
  if (value.startsWith("public/")) return `${JOBS_APP_BASE_PATH}/${value}`;
  if (value.startsWith("uploads/")) return `${PUBLIC_BASE_PATH}/${value}`;

  return "";
}

function getCompanyLogoMarkup(companyName, logoUrl, options = {}) {
  const normalizedLogoUrl = normalizeCompanyLogoUrl(logoUrl);
  if (normalizedLogoUrl) {
    return `<img src="${escapeHtml(normalizedLogoUrl)}" alt="Logo ${escapeHtml(companyName || "Cong ty")}" class="${options.imageClass || "w-full h-full object-contain"}" loading="lazy" decoding="async">`;
  }

  return `<div class="${options.fallbackClass || "w-8 h-8 rounded bg-primary flex items-center justify-center text-white font-bold"}">${escapeHtml(getCompanyInitial(companyName))}</div>`;
}

function formDataToJsonPayload(formData) {
  const data = {};
  for (const [key, value] of formData.entries()) {
    if (value instanceof File) {
      continue;
    }
    data[key] = value;
  }
  return data;
}

async function readApiJson(res) {
  const text = await res.text();
  try {
    return text ? JSON.parse(text) : {};
  } catch (err) {
    console.error("Lỗi phản hồi server:", text);
    throw new Error(
      "Lỗi hệ thống: Server trả về dữ liệu không hợp lệ (xem console).",
    );
  }
}

async function uploadCompanyLogo(file, token) {
  if (!(file instanceof File) || file.size === 0) {
    return "";
  }

  const maxSize = 2 * 1024 * 1024;
  if (file.size > maxSize) {
    throw new Error("Logo công ty tối đa 2MB.");
  }

  const formData = new FormData();
  formData.append("logo", file);

  const res = await fetch(`${API_BASE_JOBS}/upload_company_logo.php`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
    },
    body: formData,
  });

  if (res.status === 401) {
    redirectToLogin("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
    return "";
  }

  const result = await readApiJson(res);
  if (!res.ok) {
    throw new Error(result.message || "Không thể tải logo công ty lên.");
  }

  return normalizeCompanyLogoUrl(result.logo_url || "");
}

// Biến toàn cục để lưu trạng thái tìm kiếm hiện tại
let currentSearchParams = {
  keyword: "",
  location: "",
  field: [],
  experience: [],
  type: [],
  salary_range: [],
};

const FIELD_FILTER_FALLBACKS = [
  "Công nghệ thông tin,Cong nghe thong tin,IT,CNTT,Software",
  "Marketing / PR,Marketing / Truyền thông,Marketing,PR",
  "Hành chính nhân sự,Hanh chinh nhan su,Nhân sự,Nhan su,HR",
];

const EXPERIENCE_FILTER_FALLBACKS = [
  "thực tập,thuc tap,intern",
  "mới tốt nghiệp,moi tot nghiep,fresher,junior",
  "1-3 năm,1-3 nam,2-3 năm,2-3 nam,3 năm,3 nam",
  "trưởng nhóm,truong nhom,quản lý,quan ly,manager,lead",
];

function splitCsvValues(rawValue) {
  return String(rawValue || "")
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean);
}

function normalizeFilterToken(value) {
  return String(value || "").trim().toLowerCase();
}

function valuesOverlap(leftValues, rightValues) {
  const left = leftValues.map(normalizeFilterToken).filter(Boolean);
  const right = rightValues.map(normalizeFilterToken).filter(Boolean);
  if (left.length === 0 || right.length === 0) return false;
  return left.some(
    (leftToken) =>
      right.some(
        (rightToken) =>
          leftToken === rightToken ||
          leftToken.includes(rightToken) ||
          rightToken.includes(leftToken),
      ),
  );
}

function mapTopFieldValues(value) {
  switch (String(value || "").trim()) {
    case "cong-nghe-thong-tin":
      return ["Công nghệ thông tin", "Cong nghe thong tin", "IT", "CNTT"];
    case "tai-chinh-ke-toan":
      return [
        "Tài chính / Kế toán",
        "Tai chinh / Ke toan",
        "Kế toán",
        "Ke toan",
        "Finance",
      ];
    case "marketing-truyen-thong":
      return [
        "Marketing / Truyền thông",
        "Marketing / PR",
        "Marketing",
        "PR",
      ];
    case "thiet-ke":
      return ["Thiết kế", "Thiet ke", "Designer", "Design"];
    default:
      return [];
  }
}

function ensureTypeFilterInputs(filtersPanel) {
  if (
    !(filtersPanel instanceof HTMLElement) ||
    filtersPanel.querySelector('input[name="type"]')
  ) {
    return;
  }

  const firstSalaryInput = filtersPanel.querySelector('input[name="salary_range"]');
  const salarySection = firstSalaryInput?.closest("div")?.parentElement;
  if (!(salarySection instanceof HTMLElement) || !salarySection.parentElement) {
    return;
  }

  const typeSection = document.createElement("div");
  typeSection.className = "border-b border-slate-200 dark:border-slate-700 pb-5";
  typeSection.innerHTML = `
    <h4 class="font-semibold text-sm mb-3 text-slate-800 dark:text-slate-200">Hình thức làm việc</h4>
    <div class="space-y-2">
      <label class="flex items-center gap-3 cursor-pointer group">
        <input class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600" name="type" type="checkbox" value="full time,full-time,toàn thời gian,toan thoi gian" />
        <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Toàn thời gian</span>
      </label>
      <label class="flex items-center gap-3 cursor-pointer group">
        <input class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600" name="type" type="checkbox" value="part time,part-time,bán thời gian,ban thoi gian" />
        <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Bán thời gian</span>
      </label>
      <label class="flex items-center gap-3 cursor-pointer group">
        <input class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600" name="type" type="checkbox" value="remote,làm từ xa,lam tu xa,wfh" />
        <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Làm từ xa</span>
      </label>
      <label class="flex items-center gap-3 cursor-pointer group">
        <input class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600" name="type" type="checkbox" value="hybrid" />
        <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Hybrid</span>
      </label>
    </div>
  `;

  salarySection.parentElement.insertBefore(typeSection, salarySection);
}

function normalizeSearchFilterInputs() {
  const filtersPanel = document.getElementById("filters-panel");
  if (!filtersPanel) return;
  ensureTypeFilterInputs(filtersPanel);

  const fieldInputs = Array.from(
    filtersPanel.querySelectorAll('input[name="field"]'),
  );
  fieldInputs.forEach((input, index) => {
    if (!(input instanceof HTMLInputElement)) return;
    if (!FIELD_FILTER_FALLBACKS[index]) return;
    input.value = FIELD_FILTER_FALLBACKS[index];
  });

  const experienceInputs = Array.from(
    filtersPanel.querySelectorAll(
      'input[name="experience"], input[type="checkbox"]:not([name])',
    ),
  );
  experienceInputs.forEach((input, index) => {
    if (!(input instanceof HTMLInputElement)) return;
    if (!EXPERIENCE_FILTER_FALLBACKS[index]) return;
    input.name = "experience";
    input.value = EXPERIENCE_FILTER_FALLBACKS[index];
    input.checked = false;
  });
}

function detectTopFieldOption(fieldValues) {
  const tokens = fieldValues.map(normalizeFilterToken);
  const isItField = tokens.some((token) => {
    if (token.includes("công nghệ") || token.includes("cong nghe")) {
      return true;
    }
    if (token.includes("cntt")) {
      return true;
    }
    const normalizedToken = token.replace(/\s+/g, " ");
    return (
      normalizedToken === "it" ||
      normalizedToken.startsWith("it ") ||
      normalizedToken.endsWith(" it") ||
      normalizedToken.includes(" it ")
    );
  });

  if (isItField) {
    return "cong-nghe-thong-tin";
  }

  if (
    tokens.some(
      (token) =>
        token.includes("kế toán") ||
        token.includes("ke toan") ||
        token.includes("tài chính") ||
        token.includes("tai chinh") ||
        token.includes("finance"),
    )
  ) {
    return "tai-chinh-ke-toan";
  }
  if (
    tokens.some(
      (token) =>
        token.includes("marketing") ||
        token.includes("truyền thông") ||
        token.includes("truyen thong") ||
        token.includes("pr"),
    )
  ) {
    return "marketing-truyen-thong";
  }
  if (
    tokens.some(
      (token) =>
        token.includes("thiết kế") ||
        token.includes("thiet ke") ||
        token.includes("design"),
    )
  ) {
    return "thiet-ke";
  }
  return "";
}

function applySearchFiltersFromQuery() {
  const params = new URLSearchParams(window.location.search);
  if (!params.toString()) return;

  const keyword = params.get("keyword");
  const location = params.get("location");
  const fieldValues = splitCsvValues(params.get("field"));
  const experienceValues = splitCsvValues(params.get("experience"));
  const typeValues = splitCsvValues(params.get("type"));
  const salaryValues = splitCsvValues(params.get("salary_range"));

  const keywordInput = document.getElementById("keyword");
  if (keywordInput instanceof HTMLInputElement && keyword) {
    keywordInput.value = keyword;
  }

  const locationInput = document.getElementById("location");
  if (locationInput instanceof HTMLInputElement && location) {
    locationInput.value = location;
  }

  const topFieldSelect = document.getElementById("top-field");
  if (
    topFieldSelect instanceof HTMLSelectElement &&
    !topFieldSelect.value &&
    fieldValues.length > 0
  ) {
    const detectedValue = detectTopFieldOption(fieldValues);
    if (detectedValue) {
      topFieldSelect.value = detectedValue;
    }
  }

  const syncCheckedInputs = (selector, values) => {
    if (!values.length) return;
    const inputs = Array.from(document.querySelectorAll(selector));
    inputs.forEach((input) => {
      if (input instanceof HTMLInputElement) {
        const inputValues = splitCsvValues(input.value);
        input.checked = valuesOverlap(inputValues, values);
      }
    });
  };

  syncCheckedInputs('input[name="field"]', fieldValues);
  syncCheckedInputs('input[name="experience"]', experienceValues);
  syncCheckedInputs('input[name="type"]', typeValues);
  syncCheckedInputs('input[name="salary_range"]', salaryValues);
}

function clearSearchFilters() {
  const keywordInput = document.getElementById("keyword");
  const locationInput = document.getElementById("location");
  const topField = document.getElementById("top-field");

  if (keywordInput instanceof HTMLInputElement) keywordInput.value = "";
  if (locationInput instanceof HTMLInputElement) locationInput.value = "";
  if (topField instanceof HTMLSelectElement) topField.value = "";

  document
    .querySelectorAll(
      'input[name="field"], input[name="experience"], input[name="type"]',
    )
    .forEach((input) => {
      if (input instanceof HTMLInputElement) {
        input.checked = false;
      }
    });

  const salaryInputs = Array.from(
    document.querySelectorAll('input[name="salary_range"]'),
  );
  salaryInputs.forEach((input) => {
    if (input instanceof HTMLInputElement) {
      input.checked = false;
    }
  });

  const defaultSalary = document.querySelector(
    'input[name="salary_range"][value=""]',
  );
  if (defaultSalary instanceof HTMLInputElement) {
    defaultSalary.checked = true;
  }
}

// Biến toàn cục để lưu trang hiện tại của trang quản lý tin đăng
let myJobsCurrentPage = 1;
let authRedirectInProgress = false;

function redirectToLogin(message) {
  if (authRedirectInProgress) return;
  authRedirectInProgress = true;
  localStorage.removeItem("token");
  if (message) {
    alert(message);
  }
  window.location.replace("dangnhap.php");
}

/**
 * Hàm tìm kiếm công việc với phân trang
 * @param {number} page - Trang hiện tại
 */
async function searchJobs(page = 1) {
  const keyword = document.getElementById("keyword")?.value || "";
  const location = document.getElementById("location")?.value || "";
  const topFieldValues = mapTopFieldValues(
    document.getElementById("top-field")?.value || "",
  );

  // Lấy giá trị từ các bộ lọc nâng cao
  const selectedFields = Array.from(
    document.querySelectorAll('input[name="field"]:checked'),
  ).flatMap((el) => splitCsvValues(el.value));
  if (topFieldValues.length > 0) {
    selectedFields.push(...topFieldValues);
  }
  const fields = Array.from(new Set(selectedFields.filter(Boolean))).join(",");
  const experiences = Array.from(
    document.querySelectorAll('input[name="experience"]:checked'),
  )
    .flatMap((el) => splitCsvValues(el.value))
    .join(",");
  const types = Array.from(
    document.querySelectorAll('input[name="type"]:checked'),
  )
    .flatMap((el) => splitCsvValues(el.value))
    .join(",");
  const salaryRanges = Array.from(
    document.querySelectorAll('input[name="salary_range"]:checked'),
  )
    .map((el) => el.value)
    .join(",");

  // Lưu trạng thái tìm kiếm
  currentSearchParams = {
    keyword,
    location,
    field: fields,
    experience: experiences,
    type: types,
    salary_range: salaryRanges,
  };

  const url = new URL(`${API_BASE_JOBS}/jobs.php`, window.location.origin);
  if (keyword) url.searchParams.set("keyword", keyword);
  if (location) url.searchParams.set("location", location);
  url.searchParams.set("page", page);

  // Thêm các tham số lọc vào URL
  if (fields) url.searchParams.set("field", fields);
  if (experiences) url.searchParams.set("experience", experiences);
  if (types) url.searchParams.set("type", types);
  if (salaryRanges) url.searchParams.set("salary_range", salaryRanges);

  // Hiển thị loading state
  const container = document.getElementById("job-list");
  if (container) {
    container.innerHTML = `
      <div class="col-span-full flex flex-col items-center justify-center py-16 text-slate-500">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
        <p class="font-medium">Đang tải danh sách việc làm...</p>
      </div>
    `;
  }

  // Gọi AI phân tích song song (không chặn việc tìm kiếm cơ bản)
  if (keyword.length > 2) {
    analyzeKeyword(keyword).then((analysis) => {
      if (analysis && analysis.field) {
        console.log("🔍 AI Phân tích từ khóa:", analysis);
        // Có thể tự động suggest bộ lọc dựa trên AI analysis
      }
    });
  }

  try {
    const res = await fetch(url);

    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }

    const data = await res.json();

    // Kiểm tra cấu trúc response
    if (!data.success) {
      throw new Error(data.message || "Lỗi không xác định từ server");
    }

    const jobs = data.jobs || [];
    const pagination = data.pagination || null;

    if (!container) return;

    if (!Array.isArray(jobs) || jobs.length === 0) {
      container.innerHTML = `
        <div class="col-span-full text-center py-16">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
            <span class="material-symbols-outlined text-3xl text-gray-400">search_off</span>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Không tìm thấy công việc</h3>
          <p class="text-slate-500 dark:text-slate-400">Thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm của bạn</p>
        </div>
      `;
      renderJobPagination(null);
      return;
    }

    const role = getCurrentUserRole();
    const showApplyButton = role === "seeker";

    container.innerHTML = jobs
      .map((j) => {
        const detailUrl = getJobDetailUrl(j.id);
        const applyUrl = getJobApplyUrl(j.id);
        const companyLogoMarkup = getCompanyLogoMarkup(j.company, j.company_logo, {
          imageClass: "w-full h-full object-contain",
          fallbackClass:
            "w-8 h-8 rounded bg-primary flex items-center justify-center text-white font-bold",
        });

        return `
      <div class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
        <div class="flex justify-between items-start mb-4">
          <div class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
             ${companyLogoMarkup}
          </div>
          <button class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors" onclick="toggleSaveJob(${j.id})">
            <span class="material-symbols-outlined">favorite</span>
          </button>
        </div>
        <div class="mb-4">
          <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
            <a href="${detailUrl}">${escapeHtml(j.title)}</a>
          </h3>
          <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
            ${escapeHtml(j.company)}
          </p>
        </div>
        <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">location_on</span>
            <span>${escapeHtml(j.location)}</span>
          </div>
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">payments</span>
            <span>${escapeHtml(formatSalary(j.salary))}</span>
          </div>
          ${
            j.experience
              ? `
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">work_history</span>
            <span>${escapeHtml(j.experience)}</span>
          </div>
          `
              : ""
          }
        </div>
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
          <div class="${showApplyButton ? "grid grid-cols-2 gap-2" : "grid grid-cols-1"}">
            <a href="${detailUrl}" class="flex items-center justify-center w-full bg-primary hover:bg-blue-600 text-white font-bold py-2 rounded-lg transition-colors">
              Xem chi tiết
            </a>
            ${
              showApplyButton
                ? `<a href="${applyUrl}" class="flex items-center justify-center w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 rounded-lg transition-colors">Ứng tuyển</a>`
                : ""
            }
          </div>
        </div>
      </div>
    `;
      })
      .join("");

    // Render phân trang
    renderJobPagination(pagination);

    // Scroll to top of results
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  } catch (err) {
    console.error("Lỗi tải danh sách việc làm:", err);
    if (container) {
      container.innerHTML = `
        <div class="col-span-full text-center py-16">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
            <span class="material-symbols-outlined text-3xl text-red-600 dark:text-red-400">error</span>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Lỗi tải dữ liệu</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-4">${escapeHtml(err.message)}</p>
          <button onclick="searchJobs(1)" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
            Thử lại
          </button>
        </div>
      `;
    }
    renderJobPagination(null);
  }
}

/**
 * Hàm toggle save job (placeholder)
 */
function toggleSaveJob(jobId) {
  console.log("Toggle save job:", jobId);
  // TODO: Implement save job functionality
}

/**
 * Helpers for detail page rendering
 */
function splitByCommaOrLine(value) {
  return String(value || "")
    .split(/[\r\n,]+/)
    .map((item) => item.trim())
    .filter(Boolean);
}

function splitByLine(value) {
  return String(value || "")
    .split(/\r\n|\r|\n/)
    .map((item) => item.trim())
    .filter(Boolean);
}

function formatDateVi(value) {
  if (!value) return "";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return String(value);
  return date.toLocaleDateString("vi-VN");
}

function formatPostedAgo(value) {
  if (!value) return "Mới đăng";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "Mới đăng";

  const diffMs = Date.now() - date.getTime();
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  if (diffDays <= 0) return "Đăng hôm nay";
  if (diffDays === 1) return "Đăng 1 ngày trước";
  return `Đăng ${diffDays} ngày trước`;
}

function replaceMetaText(container, text) {
  if (!container || !text) return;
  const icon = container.querySelector(".material-symbols-outlined");
  if (!icon) {
    container.textContent = text;
    return;
  }
  container.textContent = "";
  container.appendChild(icon);
  container.appendChild(document.createTextNode(` ${text}`));
}

function renderTagList(container, items) {
  if (!container || !Array.isArray(items) || items.length === 0) return;
  container.innerHTML = "";
  items.forEach((item) => {
    const span = document.createElement("span");
    span.className =
      "px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded text-sm font-medium border border-gray-200 dark:border-gray-700";
    span.textContent = item;
    container.appendChild(span);
  });
}

function renderBulletList(container, items) {
  if (!container || !Array.isArray(items) || items.length === 0) return;
  container.innerHTML = "";
  items.forEach((item) => {
    const li = document.createElement("li");
    li.textContent = item;
    container.appendChild(li);
  });
}

function renderBenefits(container, lines) {
  if (!container || !Array.isArray(lines) || lines.length === 0) return;
  container.innerHTML = "";

  lines.forEach((line) => {
    const [rawTitle, rawDescription] = line.split("|");
    const title = (rawTitle || "").trim();
    const description = (rawDescription || "").trim();
    if (!title && !description) return;

    const card = document.createElement("div");
    card.className =
      "flex items-start gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors";

    const iconWrap = document.createElement("div");
    iconWrap.className =
      "size-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0";
    const icon = document.createElement("span");
    icon.className = "material-symbols-outlined text-[20px]";
    icon.textContent = "workspace_premium";
    iconWrap.appendChild(icon);

    const textWrap = document.createElement("div");
    const h4 = document.createElement("h4");
    h4.className =
      "font-semibold text-sm text-text-main-light dark:text-white";
    h4.textContent = title || "Phúc lợi";

    const p = document.createElement("p");
    p.className =
      "text-xs text-text-sub-light dark:text-text-sub-dark mt-1";
    p.textContent = description || title;

    textWrap.appendChild(h4);
    textWrap.appendChild(p);
    card.appendChild(iconWrap);
    card.appendChild(textWrap);
    container.appendChild(card);
  });
}

function normalizeSearchText(value) {
  return String(value || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "");
}

function buildSimilarJobQueryStrategies(job) {
  const field = String(job?.field || "").trim();
  const jobType = String(job?.job_type || "").trim();
  const location = String(job?.location || "").trim();
  const titleKeyword = String(job?.title || "")
    .trim()
    .split(/\s+/)
    .slice(0, 4)
    .join(" ");

  const candidates = [
    { field, type: jobType, location },
    { field, type: jobType },
    { field, location },
    { field },
    { type: jobType, location },
    { type: jobType },
    { location },
    titleKeyword.length >= 3 ? { keyword: titleKeyword } : null,
    {},
  ].filter(Boolean);

  const unique = [];
  const seen = new Set();
  candidates.forEach((item) => {
    const filtered = Object.fromEntries(
      Object.entries(item).filter(([, value]) => String(value || "").trim() !== ""),
    );
    const key = JSON.stringify(filtered);
    if (seen.has(key)) return;
    seen.add(key);
    unique.push(filtered);
  });

  return unique;
}

async function fetchJobsForSimilar(filters) {
  const url = new URL(`${API_BASE_JOBS}/jobs.php`, window.location.origin);
  url.searchParams.set("page", "1");

  Object.entries(filters || {}).forEach(([key, value]) => {
    if (String(value || "").trim() !== "") {
      url.searchParams.set(key, value);
    }
  });

  try {
    const res = await fetch(url);
    if (!res.ok) {
      return [];
    }

    const data = await res.json();
    if (!data || data.success !== true || !Array.isArray(data.jobs)) {
      return [];
    }

    return data.jobs;
  } catch (error) {
    console.warn("Không tải được danh sách việc làm tương tự:", error);
    return [];
  }
}

function getBadgeTextForJob(title) {
  const words = String(title || "")
    .trim()
    .split(/\s+/)
    .filter(Boolean);

  if (words.length >= 2) {
    return `${words[0][0] || ""}${words[1][0] || ""}`.toUpperCase();
  }

  if (words.length === 1) {
    return words[0].slice(0, 2).toUpperCase();
  }

  return "JOB";
}

function renderSimilarJobsList(container, jobs) {
  if (!container) return;

  if (!Array.isArray(jobs) || jobs.length === 0) {
    container.innerHTML = `
      <p class="text-sm text-text-sub-light dark:text-text-sub-dark">
        Chưa có việc làm tương tự phù hợp.
      </p>
    `;
    return;
  }

  const themes = [
    {
      boxClass:
        "size-11 rounded bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 font-bold text-xs shrink-0 border border-indigo-100 dark:border-indigo-800",
    },
    {
      boxClass:
        "size-11 rounded bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 font-bold text-xs shrink-0 border border-orange-100 dark:border-orange-800",
    },
    {
      boxClass:
        "size-11 rounded bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 font-bold text-xs shrink-0 border border-emerald-100 dark:border-emerald-800",
    },
  ];

  container.innerHTML = jobs
    .map((job, index) => {
      const theme = themes[index % themes.length];
      const detailUrl = getJobDetailUrl(job.id);
      const title = escapeHtml(job.title || "Vị trí đang cập nhật");
      const company = escapeHtml(job.company || "Đang cập nhật");
      const location = escapeHtml(job.location || "Đang cập nhật");
      const salary = escapeHtml(formatSalary(job.salary));
      const badgeText = escapeHtml(getBadgeTextForJob(job.title));

      return `
        <a class="block group" href="${detailUrl}">
          <div class="flex gap-3">
            <div class="${theme.boxClass}">
              ${badgeText}
            </div>
            <div class="flex-1">
              <h4 class="font-semibold text-sm text-text-main-light dark:text-white group-hover:text-primary transition-colors line-clamp-1">
                ${title}
              </h4>
              <p class="text-xs text-text-sub-light dark:text-text-sub-dark mt-0.5">
                ${company}
              </p>
              <div class="flex justify-between items-center mt-1 gap-2">
                <span class="text-[10px] bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-gray-500 line-clamp-1">
                  ${location}
                </span>
                <p class="text-xs font-bold text-primary whitespace-nowrap">
                  ${salary}
                </p>
              </div>
            </div>
          </div>
        </a>
      `;
    })
    .join('<hr class="border-gray-100 dark:border-gray-800" />');
}

function bindSimilarJobsButton(button, sourceJob) {
  if (!button) return;

  const url = new URL(`${PUBLIC_BASE_PATH}/timkiem.php`, window.location.origin);
  const field = String(sourceJob?.field || "").trim();
  const jobType = String(sourceJob?.job_type || "").trim();
  const location = String(sourceJob?.location || "").trim();

  if (field) url.searchParams.set("field", field);
  if (jobType) url.searchParams.set("type", jobType);
  if (location) url.searchParams.set("location", location);

  button.onclick = () => {
    window.location.href = `${url.pathname}${url.search}`;
  };
}

async function loadAndRenderSimilarJobs(sourceJob, currentJobId, similarCard) {
  if (!similarCard) return;

  const listContainer = similarCard.querySelector(".space-y-4");
  const viewAllButton = similarCard.querySelector("button");
  if (!listContainer) return;

  bindSimilarJobsButton(viewAllButton, sourceJob);

  listContainer.innerHTML = `
    <p class="text-sm text-text-sub-light dark:text-text-sub-dark">
      Đang tải việc làm tương tự...
    </p>
  `;

  const targetCount = 2;
  const relatedJobs = [];
  const seenIds = new Set([String(currentJobId)]);
  const strategies = buildSimilarJobQueryStrategies(sourceJob);

  for (const strategy of strategies) {
    if (relatedJobs.length >= targetCount) break;
    const fetched = await fetchJobsForSimilar(strategy);

    fetched.forEach((job) => {
      if (relatedJobs.length >= targetCount) return;
      const id = String(job?.id ?? "");
      if (!id || seenIds.has(id)) return;
      seenIds.add(id);
      relatedJobs.push(job);
    });
  }

  renderSimilarJobsList(listContainer, relatedJobs.slice(0, targetCount));
}

function findSimilarJobsCard(sideCards) {
  if (!sideCards || sideCards.length === 0) return null;

  const matchedByTitle = Array.from(sideCards).find((card) => {
    const heading = card.querySelector("h3");
    return normalizeSearchText(heading?.textContent || "").includes("tuong tu");
  });

  return matchedByTitle || sideCards[3] || null;
}

/**
 * Hàm tải chi tiết công việc
 */
async function loadJobDetail(id) {
  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${id}`);
    if (!res.ok) throw new Error("Không thể tải chi tiết công việc");
    const j = await res.json();

    if (!j) {
      alert("Công việc không tồn tại hoặc đã bị xóa.");
      return;
    }

    const title = j.title || "";
    const company = j.company || "";
    const companyLogo = normalizeCompanyLogoUrl(j.company_logo);
    const location = j.location || "Đang cập nhật";
    const salary = j.salary || "";
    const jobType = j.job_type || "Đang cập nhật";
    const experience = j.experience || "Đang cập nhật";
    const level = j.level || j.job_level || "Đang cập nhật";
    const deadlineText = j.deadline ? formatDateVi(j.deadline) : "Đang cập nhật";
    const postedText = formatPostedAgo(j.created_at);

    const titleEl = document.getElementById("job-title");
    const companyEl = document.getElementById("job-company");
    const salaryEl = document.getElementById("job-salary");
    const locationEl = document.getElementById("job-location");
    const companyLogoEl = document.getElementById("job-company-logo");
    const companySideLogoEl = document.getElementById("job-company-side-logo");
    if (titleEl) titleEl.textContent = title;
    if (companyEl) companyEl.textContent = company;
    if (salaryEl) salaryEl.textContent = formatSalary(salary);
    if (locationEl) locationEl.textContent = location;
    if (companyLogoEl) {
      companyLogoEl.innerHTML = getCompanyLogoMarkup(company, companyLogo, {
        imageClass: "w-full h-full object-contain",
        fallbackClass:
          "w-full h-full flex items-center justify-center bg-gray-900 rounded text-white text-4xl font-black tracking-tight",
      });
    }
    if (companySideLogoEl) {
      companySideLogoEl.innerHTML = getCompanyLogoMarkup(company, companyLogo, {
        imageClass: "w-full h-full object-contain",
        fallbackClass:
          "w-full h-full flex items-center justify-center bg-gray-900 rounded text-white text-sm font-black tracking-tight",
      });
    }

    const metaItems = document.querySelectorAll(
      "div.flex.flex-wrap.items-center.gap-x-6.gap-y-2 > span",
    );
    replaceMetaText(metaItems[0], location);
    replaceMetaText(metaItems[1], postedText);

    const overviewGrid = salaryEl ? salaryEl.closest(".grid") : null;
    const overviewCards = overviewGrid
      ? overviewGrid.querySelectorAll(":scope > div")
      : [];
    if (overviewCards[1]) {
      const p = overviewCards[1].querySelector("p");
      if (p) p.textContent = jobType;
    }
    if (overviewCards[2]) {
      const p = overviewCards[2].querySelector("p");
      if (p) p.textContent = experience;
    }
    if (overviewCards[3]) {
      const p = overviewCards[3].querySelector("p");
      if (p) p.textContent = level;
    }
    if (overviewCards[5]) {
      const p = overviewCards[5].querySelector("p");
      if (p) p.textContent = deadlineText;
    }

    const deadlineStrong = document.querySelector("#apply-section p strong");
    if (deadlineStrong) deadlineStrong.textContent = deadlineText;

    const descriptionEl = document.getElementById("job-description");
    if (descriptionEl && j.description) {
      descriptionEl.innerHTML = "";
      splitByLine(j.description).forEach((line) => {
        const p = document.createElement("p");
        p.className = "mb-3";
        p.textContent = line;
        descriptionEl.appendChild(p);
      });
    }

    const sideCards = document.querySelectorAll(".lg\\:col-span-4 > .rounded-xl");
    const companyCard = sideCards[1];
    if (companyCard) {
      const sideCompanyTitle = companyCard.querySelector("h4");
      if (sideCompanyTitle) sideCompanyTitle.textContent = company || "Công ty";

      const rows = companyCard.querySelectorAll(".flex.justify-between");
      const companySize = j.company_size || "Đang cập nhật";
      const companyIndustry = j.company_industry || "Đang cập nhật";
      if (rows[0]) {
        const valueEl = rows[0].querySelector("span:last-child");
        if (valueEl) valueEl.textContent = companySize;
      }
      if (rows[1]) {
        const valueEl = rows[1].querySelector("span:last-child");
        if (valueEl) valueEl.textContent = companyIndustry;
      }
      if (rows[2]) {
        const valueEl = rows[2].querySelector("span:last-child");
        if (valueEl) valueEl.textContent = location;
      }
    }

    const candidateCard = sideCards[2];
    if (candidateCard) {
      const paragraphs = candidateCard.querySelectorAll("p");
      if (paragraphs[0] && j.candidate_profile) {
        paragraphs[0].textContent = j.candidate_profile;
      }
      if (paragraphs[1] && j.candidate_bonus) {
        const label = paragraphs[1].querySelector("span")?.cloneNode(true);
        paragraphs[1].textContent = "";
        if (label) {
          paragraphs[1].appendChild(label);
          paragraphs[1].appendChild(document.createTextNode(` ${j.candidate_bonus}`));
        } else {
          paragraphs[1].textContent = j.candidate_bonus;
        }
      }
    }

    const requirementSectionTitle = Array.from(document.querySelectorAll("h3"))
      .find((h3) => normalizeSearchText(h3.textContent).includes("yeu cau"));
    const requirementSection = requirementSectionTitle
      ? requirementSectionTitle.closest("section")
      : null;
    if (requirementSection) {
      const skillsContainer = requirementSection.querySelector(".flex.flex-wrap.gap-2");
      const requirementLists = requirementSection.querySelectorAll("ul.list-disc");

      renderTagList(skillsContainer, splitByCommaOrLine(j.skills));
      renderBulletList(requirementLists[0], splitByLine(j.education_requirements));
      renderBulletList(requirementLists[1], splitByLine(j.soft_skills));
    }

    const benefitsSectionTitle = Array.from(document.querySelectorAll("h3"))
      .find((h3) => normalizeSearchText(h3.textContent).includes("phuc loi"));
    const benefitsSection = benefitsSectionTitle
      ? benefitsSectionTitle.closest("section")
      : null;
    if (benefitsSection) {
      const benefitsGrid = benefitsSection.querySelector(".grid.sm\\:grid-cols-2.gap-4");
      renderBenefits(benefitsGrid, splitByLine(j.benefits));
    }

    const similarCard = findSimilarJobsCard(sideCards);
    await loadAndRenderSimilarJobs(j, Number(id), similarCard);
  } catch (err) {
    console.error(err);
    alert("Không thể tải thông tin công việc: " + err.message);
  }
}

/**
 * Hàm tạo công việc mới
 */
async function createJob(e) {
  e.preventDefault();
  const token = localStorage.getItem("token");
  if (!token) {
    redirectToLogin("Vui lòng đăng nhập lại.");
    return;
  }

  const formData = new FormData(e.target);
  const data = formDataToJsonPayload(formData);
  const logoFile = formData.get("company_logo_file");

  try {
    if (logoFile instanceof File && logoFile.size > 0) {
      data.company_logo = await uploadCompanyLogo(logoFile, token);
    }

    const res = await fetch(`${API_BASE_JOBS}/jobs.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });

    const result = await readApiJson(res);

    if (res.status === 401) {
      redirectToLogin("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
      return;
    }

    if (res.ok) {
      alert("Đăng tin thành công!");
      window.location.href = "quanlytindang.php";
    } else {
      alert(result.message || "Có lỗi xảy ra.");
    }
  } catch (err) {
    console.error(err);
    alert(err.message || "Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Hàm cập nhật công việc
 */
async function updateJob(e, jobId) {
  e.preventDefault();
  const token = localStorage.getItem("token");
  if (!token) {
    redirectToLogin("Vui lòng đăng nhập lại.");
    return;
  }

  const formData = new FormData(e.target);
  const data = formDataToJsonPayload(formData);
  const logoFile = formData.get("company_logo_file");

  try {
    if (logoFile instanceof File && logoFile.size > 0) {
      data.company_logo = await uploadCompanyLogo(logoFile, token);
    }

    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${jobId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });

    const result = await readApiJson(res);

    if (res.status === 401) {
      redirectToLogin("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
      return;
    }

    if (res.ok) {
      alert("Cập nhật tin thành công!");
      window.location.href = "quanlytindang.php";
    } else {
      alert(result.message || "Có lỗi xảy ra.");
    }
  } catch (err) {
    console.error(err);
    alert(err.message || "Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Hàm tải danh sách công việc đã đăng của nhà tuyển dụng
 * @param {number} page - Trang hiện tại
 */
async function loadMyJobs(page = 1) {
  const token = localStorage.getItem("token");
  if (!token) {
    redirectToLogin("Bạn cần đăng nhập để xem trang này.");
    return;
  }

  // Lưu lại trang hiện tại để có thể reload
  myJobsCurrentPage = page;

  const container = document.getElementById("my-jobs-list");
  if (!container) return;

  // Hiển thị trạng thái tải
  container.innerHTML = `
    <tr>
      <td colspan="5" class="text-center py-16">
        <div class="flex flex-col items-center justify-center text-slate-500">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
          <p class="font-medium">Đang tải danh sách tin đã đăng...</p>
        </div>
      </td>
    </tr>
  `;

  const url = new URL(`${API_BASE_JOBS}/jobs.php`, window.location.origin);
  url.searchParams.set("view", "employer");
  url.searchParams.set("page", page);

  try {
    const res = await fetch(url, {
      headers: { Authorization: `Bearer ${token}` },
    });

    if (!res.ok) {
      const errorData = await res
        .json()
        .catch(() => ({ message: "Lỗi không xác định từ máy chủ." }));
      if (res.status === 401) {
        redirectToLogin("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
        return;
      }
      throw new Error(errorData.message || `Lỗi máy chủ: ${res.status}`);
    }

    const data = await res.json();
    if (!data.success) {
      throw new Error(data.message || "Lỗi không xác định từ server");
    }

    const jobs = data.jobs || [];
    const pagination = data.pagination || null;

    if (jobs.length === 0) {
      container.innerHTML = `
        <tr>
          <td colspan="5" class="text-center py-16">
            <div class="flex flex-col items-center justify-center text-slate-500">
              <span class="material-symbols-outlined text-5xl mb-4">post_add</span>
              <p class="text-lg font-medium">Bạn chưa đăng tin tuyển dụng nào.</p>
              <a href="dangtin.php#post-job-section" class="mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                Đăng tin ngay
              </a>
            </div>
          </td>
        </tr>
      `;
      renderMyJobsPagination(null);
      return;
    }

    // Giả sử container là một <tbody>
    container.innerHTML = jobs
      .map((job) => {
        const applicantCount = Math.max(
          0,
          parseInt(job.applicant_count ?? 0, 10) || 0,
        );

        return `
      <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
        <td class="p-4">
          <p class="font-bold text-slate-800 dark:text-slate-200">${escapeHtml(job.title)}</p>
          <p class="text-sm text-slate-500">${escapeHtml(job.location)}</p>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">${new Date(job.created_at).toLocaleDateString("vi-VN")}</td>
        <td class="p-4">
          <span class="px-2 py-1 text-xs font-semibold rounded-full ${job.status === "open" ? "bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300" : "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"}">
            ${job.status === "open" ? "Đang hiển thị" : "Đã đóng"}
          </span>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">${applicantCount}</td>
        <td class="p-4 text-right">
          <div class="flex items-center justify-end gap-4">
            <a href="${PUBLIC_BASE_PATH}/xemungvien.php?job_id=${job.id}" class="text-sm font-medium text-primary hover:underline">Xem ứng viên</a>
            <a href="${PUBLIC_BASE_PATH}/suatin.php?id=${job.id}" class="text-sm font-medium text-slate-500 hover:text-primary">Sửa</a>
            <button onclick="toggleJobStatus(${job.id}, '${job.status}')" class="text-sm font-medium ${job.status === "open" ? "text-red-500 hover:text-red-700" : "text-green-500 hover:text-green-700"} transition-colors">
              ${job.status === "open" ? "Đóng tin" : "Mở lại"}
            </button>
            <button onclick="deleteJob(${job.id})" class="text-sm font-medium text-gray-400 hover:text-red-600 transition-colors" title="Xóa tin">
              Xóa
            </button>
          </div>
        </td>
      </tr>
    `;
      })
      .join("");

    renderMyJobsPagination(pagination);
  } catch (e) {
    console.error("Lỗi tải tin đã đăng:", e);
    container.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-16">
          <div class="flex flex-col items-center justify-center text-red-500">
            <span class="material-symbols-outlined text-5xl mb-4">error</span>
            <h3 class="text-lg font-semibold mb-2">Lỗi tải dữ liệu</h3>
            <p class="text-slate-500 mb-4">${escapeHtml(e.message)}</p>
            <button onclick="loadMyJobs(1)" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
              Thử lại
            </button>
          </div>
        </td>
      </tr>
    `;
    renderMyJobsPagination(null);
  }
}

/**
 * Hàm Đóng/Mở tin tuyển dụng
 * @param {number} jobId ID của công việc
 * @param {string} currentStatus Trạng thái hiện tại ('open' hoặc 'closed')
 */
async function toggleJobStatus(jobId, currentStatus) {
  const token = localStorage.getItem("token");
  if (!token) {
    redirectToLogin("Vui lòng đăng nhập lại để thực hiện thao tác này.");
    return;
  }

  const newStatus = currentStatus === "open" ? "closed" : "open";
  const actionText = newStatus === "closed" ? "đóng" : "mở lại";

  if (!confirm(`Bạn có chắc muốn ${actionText} tin tuyển dụng này không?`)) {
    return;
  }

  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${jobId}`, {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ status: newStatus }),
    });

    const result = await res.json();

    if (res.status === 401) {
      redirectToLogin("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
      return;
    }

    if (res.ok) {
      alert(result.message || "Cập nhật trạng thái thành công!");
      loadMyJobs(myJobsCurrentPage); // Tải lại danh sách ở trang hiện tại
    } else {
      alert(result.message || "Có lỗi xảy ra, không thể cập nhật.");
    }
  } catch (err) {
    console.error("Lỗi khi cập nhật trạng thái công việc:", err);
    alert("Lỗi kết nối đến máy chủ. Vui lòng thử lại.");
  }
}

/**
 * Hàm xóa mềm tin tuyển dụng
 * @param {number} jobId ID của công việc
 */
async function deleteJob(jobId) {
  const token = localStorage.getItem("token");
  if (!token) {
    redirectToLogin("Vui lòng đăng nhập lại.");
    return;
  }

  if (
    !confirm(
      "Bạn có chắc chắn muốn xóa tin tuyển dụng này? Hành động này sẽ ẩn tin khỏi hệ thống.",
    )
  ) {
    return;
  }

  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${jobId}`, {
      method: "DELETE",
      headers: { Authorization: `Bearer ${token}` },
    });
    const data = await res.json().catch(() => ({}));

    if (res.status === 401) {
      redirectToLogin("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
      return;
    }

    if (!res.ok) {
      alert(data.message || "Có lỗi xảy ra, không thể xóa tin.");
      return;
    }

    alert(data.message || "Đã xóa thành công!");
    loadMyJobs(myJobsCurrentPage);
  } catch (err) {
    console.error(err);
    alert("Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Hàm render phân trang cho danh sách công việc của nhà tuyển dụng
 */
function renderMyJobsPagination(pagination) {
  const container = document.getElementById("pagination-container-my-jobs");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = "";
    return;
  }

  const { page, totalPages, totalJobs, limit, hasNextPage, hasPrevPage } =
    pagination;
  const startItem = (page - 1) * limit + 1;
  const endItem = Math.min(page * limit, totalJobs);

  let paginationHTML = `
    <div class="flex flex-col sm:flex-row sm:flex-1 sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Hiển thị <span class="font-medium">${startItem}</span> đến
                <span class="font-medium">${endItem}</span> trong số
                <span class="font-medium">${totalJobs}</span> tin đã đăng
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
  `;

  // Previous button
  paginationHTML += `
    <a href="#" 
       onclick="event.preventDefault(); ${hasPrevPage ? `loadMyJobs(${page - 1})` : "return false;"}" 
       class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasPrevPage ? "pointer-events-none opacity-50" : ""}">
        <span class="sr-only">Previous</span>
        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
    </a>`;

  const pageNumbers = getPageNumbers(page, totalPages);
  pageNumbers.forEach((pageNum) => {
    if (pageNum === "...") {
      paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">...</span>`;
    } else if (pageNum === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${pageNum}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); loadMyJobs(${pageNum})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 transition-colors">${pageNum}</a>`;
    }
  });

  // Next button
  paginationHTML += `
    <a href="#" 
       onclick="event.preventDefault(); ${hasNextPage ? `loadMyJobs(${page + 1})` : "return false;"}" 
       class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasNextPage ? "pointer-events-none opacity-50" : ""}">
        <span class="sr-only">Next</span>
        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
    </a>`;

  paginationHTML += `</nav></div></div>`;
  container.innerHTML = paginationHTML;
}

/**
 * Hàm phân tích từ khóa bằng AI
 */
async function analyzeKeyword(keyword) {
  try {
    const res = await fetch(`${API_BASE_JOBS}/analyze.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ keyword }),
    });
    const data = await res.json();
    return data;
  } catch (err) {
    console.error("Lỗi phân tích từ khóa:", err);
    return null;
  }
}

/**
 * Hàm render phân trang cho danh sách công việc
 */
function renderJobPagination(pagination) {
  const container = document.getElementById("pagination-container-jobs");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = ""; // Không cần phân trang
    return;
  }

  const { page, totalPages, totalJobs, limit, hasNextPage, hasPrevPage } =
    pagination;

  // Tính toán số hiển thị
  const startItem = (page - 1) * limit + 1;
  const endItem = Math.min(page * limit, totalJobs);

  let paginationHTML = `
    <div class="flex flex-col sm:flex-row sm:flex-1 sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Hiển thị <span class="font-medium">${startItem}</span> đến
                <span class="font-medium">${endItem}</span> trong số
                <span class="font-medium">${totalJobs}</span> công việc
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
  `;

  // Nút Previous
  paginationHTML += `
        <a href="#" 
           onclick="event.preventDefault(); ${hasPrevPage ? `searchJobs(${page - 1})` : "return false;"}" 
           class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasPrevPage ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Previous</span>
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>`;

  // Tạo danh sách các trang hiển thị
  const pageNumbers = getPageNumbers(page, totalPages);

  pageNumbers.forEach((pageNum) => {
    if (pageNum === "...") {
      paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">...</span>`;
    } else if (pageNum === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${pageNum}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); searchJobs(${pageNum})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 transition-colors">${pageNum}</a>`;
    }
  });

  // Nút Next
  paginationHTML += `
        <a href="#" 
           onclick="event.preventDefault(); ${hasNextPage ? `searchJobs(${page + 1})` : "return false;"}" 
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
 * Khởi tạo khi trang được load
 */
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("job-list")) {
    normalizeSearchFilterInputs();
    applySearchFiltersFromQuery();

    const toggleFilterButton = document.getElementById("toggle-filter-btn");
    const filtersPanel = document.getElementById("filters-panel");
    if (
      toggleFilterButton instanceof HTMLButtonElement &&
      filtersPanel instanceof HTMLElement
    ) {
      toggleFilterButton.addEventListener("click", () => {
        const isHidden = filtersPanel.classList.contains("hidden");
        filtersPanel.classList.toggle("hidden", !isHidden);
      });
    }

    const clearFiltersButton = document.getElementById("clear-filters-btn");
    if (clearFiltersButton instanceof HTMLButtonElement) {
      clearFiltersButton.addEventListener("click", () => {
        clearSearchFilters();
        searchJobs(1);
      });
    }

    // Gắn sự kiện cho form tìm kiếm chính
    const searchForm = document.getElementById("search-form");
    if (searchForm) {
      searchForm.addEventListener("submit", (e) => {
        e.preventDefault();
        searchJobs(1); // Tìm kiếm lại từ trang 1
      });
    }

    // Gắn sự kiện cho nút tìm kiếm (nếu có)
    const searchButton = document.querySelector(
      'button[onclick="searchJobs()"]',
    );
    if (searchButton) {
      searchButton.onclick = (e) => {
        e.preventDefault();
        searchJobs(1);
      };
    }

    ["keyword", "location"].forEach((inputId) => {
      const input = document.getElementById(inputId);
      if (input instanceof HTMLInputElement) {
        input.addEventListener("keydown", (event) => {
          if (event.key !== "Enter") return;
          event.preventDefault();
          searchJobs(1);
        });
      }
    });

    // Tự động tìm kiếm khi người dùng đổi bộ lọc (checkbox/radio)
    document.addEventListener("change", (event) => {
      const target = event.target;
      if (target instanceof HTMLSelectElement && target.id === "top-field") {
        searchJobs(1);
        return;
      }
      if (!(target instanceof HTMLInputElement)) return;

      const isFilterCheckbox =
        target.type === "checkbox" &&
        ["field", "experience", "type"].includes(target.name);
      const isSalaryRadio =
        target.type === "radio" && target.name === "salary_range";

      if (isFilterCheckbox || isSalaryRadio) {
        searchJobs(1); // Tìm kiếm lại từ trang 1 khi bộ lọc thay đổi
      }
    });

    // Tải danh sách ban đầu
    searchJobs(1);
  }

  // Khởi tạo cho trang quản lý tin đăng của nhà tuyển dụng
  if (document.getElementById("my-jobs-list")) {
    loadMyJobs(1);
  }
});



