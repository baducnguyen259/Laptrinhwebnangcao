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

const AUTH_APP_BASE_PATH = resolveAppBasePath();
const API_BASE = `${AUTH_APP_BASE_PATH}/api`;

function getFormValue(form, names) {
  const list = Array.isArray(names) ? names : [names];
  for (const name of list) {
    const checked = form.querySelector(`input[name="${name}"]:checked`);
    if (checked && typeof checked.value === "string") {
      const v = checked.value.trim();
      if (v) return v;
    }

    const el =
      form.querySelector(`[name="${name}"]`) || form.querySelector(`#${name}`);
    if (el && typeof el.value === "string") {
      const v = el.value.trim();
      if (v) return v;
    }
  }
  return "";
}

function decodeToken(token) {
  try {
    const base64Url = token.split(".")[1];
    if (!base64Url) return null;
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    const payload = decodeURIComponent(
      atob(base64)
        .split("")
        .map((c) => `%${`00${c.charCodeAt(0).toString(16)}`.slice(-2)}`)
        .join(""),
    );
    return JSON.parse(payload);
  } catch (_) {
    return null;
  }
}

function getTokenRole(tokenOrPayload) {
  const payload =
    typeof tokenOrPayload === "string"
      ? decodeToken(tokenOrPayload)
      : tokenOrPayload;
  return payload?.data?.role || payload?.role || null;
}

function isTokenExpired(payload) {
  const exp = Number(payload?.exp);
  if (!Number.isFinite(exp)) return false;
  return Date.now() >= exp * 1000;
}

function getCurrentPage() {
  const parts = window.location.pathname.split("/");
  return (parts[parts.length - 1] || "").toLowerCase();
}

function isRestrictedEmployerPage(page) {
  return ["dangtin.php", "quanlytindang.php", "xemungvien.php", "suatin.php"].includes(page);
}

function isRestrictedSeekerPage(page) {
  return ["trangthai.php"].includes(page);
}

function buildAuthButtonsByRole(role) {
  if (role === "employer") {
    return `
      <a href="quanlytindang.php" class="hidden lg:block text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary mr-2">
          Quản lý tin
      </a>
      <a href="dangtin.php" class="hidden sm:flex h-9 px-4 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors mr-2">
          Đăng tin
      </a>
      <button id="logoutBtn" class="h-9 px-4 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-200 transition-colors">
          Đăng xuất
      </button>
    `;
  }

  return `
    <a href="trangthai.php" class="hidden lg:block text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary mr-2">
        Hồ sơ của tôi
    </a>
    <button id="logoutBtn" class="h-9 px-4 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-200 transition-colors">
        Đăng xuất
    </button>
  `;
}

function bindLogout() {
  const logoutBtn = document.getElementById("logoutBtn");
  if (!logoutBtn) return;
  logoutBtn.addEventListener("click", () => {
    localStorage.removeItem("token");
    window.location.href = "dangnhap.php";
  });
}

const registerForm = document.getElementById("registerForm");
if (registerForm) {
  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const form = e.currentTarget;
    const pickedRole = getFormValue(form, "role");
    const role = pickedRole === "employer" ? "employer" : "seeker";
    const data = {
      name: getFormValue(form, ["fullname", "name", "full_name"]),
      email: getFormValue(form, "email"),
      password: getFormValue(form, "password"),
      role,
    };

    if (!data.name || !data.email || !data.password) {
      alert("Vui lòng nhập đầy đủ Họ tên, Email và Mật khẩu.");
      return;
    }

    try {
      const res = await fetch(`${API_BASE}/auth.php?action=register`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const text = await res.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (_) {
        throw new Error("Server trả về dữ liệu không hợp lệ.");
      }

      if (res.ok) {
        alert("Đăng ký thành công! Đang chuyển đến trang đăng nhập...");
        window.location.href = "dangnhap.php";
      } else {
        alert(result.message || "Đăng ký thất bại");
      }
    } catch (error) {
      console.error("Lỗi:", error);
      alert("Lỗi: " + error.message);
    }
  });
}

const loginForm = document.getElementById("loginForm");
if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const form = e.currentTarget;
    const data = {
      email: getFormValue(form, "email"),
      password: getFormValue(form, "password"),
    };

    if (!data.email || !data.password) {
      alert("Vui lòng nhập đầy đủ Email và Mật khẩu.");
      return;
    }

    try {
      const res = await fetch(`${API_BASE}/auth.php?action=login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const text = await res.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (_) {
        throw new Error("Lỗi hệ thống: Server trả về dữ liệu không hợp lệ.");
      }

      if (res.ok && result.token) {
        localStorage.setItem("token", result.token);
        const role = getTokenRole(result.token);
        window.location.href = role === "employer" ? "quanlytindang.php" : "timkiem.php";
      } else {
        alert(result.message || "Đăng nhập thất bại");
      }
    } catch (error) {
      console.error("Lỗi đăng nhập:", error);
      alert(error.message || "Có lỗi xảy ra khi kết nối đến máy chủ.");
    }
  });
}

function checkAuth() {
  const token = localStorage.getItem("token");
  const authButtons = document.getElementById("auth-buttons");
  const page = getCurrentPage();

  if (!token) {
    if (isRestrictedEmployerPage(page) || isRestrictedSeekerPage(page)) {
      window.location.href = "dangnhap.php";
    }
    return;
  }

  const payload = decodeToken(token);
  const role = getTokenRole(payload);
  if (!payload || !role || isTokenExpired(payload)) {
    localStorage.removeItem("token");
    if (page !== "dangnhap.php" && page !== "dangky.php") {
      window.location.replace("dangnhap.php");
    }
    return;
  }

  if (page === "dangnhap.php" || page === "dangky.php") {
    window.location.href = role === "employer" ? "quanlytindang.php" : "timkiem.php";
    return;
  }

  if (isRestrictedEmployerPage(page) && role !== "employer") {
    alert("Chỉ nhà tuyển dụng mới có quyền truy cập trang này.");
    window.location.href = "timkiem.php";
    return;
  }

  if (isRestrictedSeekerPage(page) && role !== "seeker") {
    alert("Trang này chỉ dành cho ứng viên.");
    window.location.href = "quanlytindang.php";
    return;
  }

  if (authButtons) {
    authButtons.innerHTML = buildAuthButtonsByRole(role);
    bindLogout();
  }
}

document.addEventListener("DOMContentLoaded", checkAuth);


