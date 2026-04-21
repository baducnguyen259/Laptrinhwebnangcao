/**
 * Hàm escape HTML để tránh XSS
 * @param {string} text - Chuỗi cần escape
 * @returns {string} Chuỗi đã được escape
 */
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text ? String(text).replace(/[&<>"']/g, (m) => map[m]) : "";
}

/**
 * Định dạng hiển thị lương sang VND
 * @param {string|number} salary - Giá trị lương
 * @returns {string} Chuỗi định dạng
 */
function formatSalary(salary) {
  if (!salary) return "Thỏa thuận";
  // Kiểm tra nếu là số hoặc chuỗi số (ví dụ "10000000")
  if (!isNaN(salary) && !isNaN(parseFloat(salary))) {
    return new Intl.NumberFormat("vi-VN", {
      style: "currency",
      currency: "VND",
    })
      .format(salary)
      .replace("₫", "VNĐ");
  }
  return salary;
}

/**
 * Hàm tính toán danh sách số trang hiển thị cho phân trang
 * @param {number} currentPage - Trang hiện tại
 * @param {number} totalPages - Tổng số trang
 * @returns {Array<number|string>} Mảng chứa các số trang và dấu "..."
 */
function getPageNumbers(currentPage, totalPages) {
  const pages = [];
  const maxVisible = 7; // Số trang tối đa hiển thị

  if (totalPages <= maxVisible) {
    // Hiển thị tất cả nếu ít hơn maxVisible
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i);
    }
  } else {
    // Luôn hiển thị trang đầu
    pages.push(1);

    if (currentPage > 3) {
      pages.push("...");
    }

    // Hiển thị các trang xung quanh trang hiện tại
    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
      pages.push(i);
    }

    if (currentPage < totalPages - 2) {
      pages.push("...");
    }

    // Luôn hiển thị trang cuối
    pages.push(totalPages);
  }

  return pages;
}
