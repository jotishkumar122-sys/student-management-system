// ===== Student Management System — script.js =====

// ----- Responsive nav toggle -----
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');
if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => navLinks.classList.toggle('open'));
}

// ----- Delete confirmation -----
document.querySelectorAll('form[data-confirm]').forEach((form) => {
  form.addEventListener('submit', (e) => {
    if (!confirm(form.dataset.confirm)) e.preventDefault();
  });
});

// ----- Loading indicator on form submit buttons -----
// Any submit button with [data-loading-text] gets disabled + a spinner while the
// page navigates away, so the user gets feedback instead of a frozen-looking page.
document.querySelectorAll('form').forEach((form) => {
  form.addEventListener('submit', () => {
    if (!form.checkValidity || form.checkValidity()) {
      const btn = form.querySelector('button[type="submit"][data-loading-text]');
      if (btn && !btn.classList.contains('loading')) {
        btn.classList.add('loading');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> ' + btn.dataset.loadingText;
      }
    }
  });
});

// ----- Client-side validation helpers (mirrors server-side rules) -----
function showFieldError(input, message) {
  const group = input.closest('.form-group');
  if (!group) return;
  group.classList.add('error');
  let msg = group.querySelector('.error-msg');
  if (!msg) {
    msg = document.createElement('div');
    msg.className = 'error-msg';
    group.appendChild(msg);
  }
  msg.textContent = message;
}
function clearFieldError(input) {
  const group = input.closest('.form-group');
  if (!group) return;
  group.classList.remove('error');
}

function validateForm(form, rules) {
  let valid = true;
  rules.forEach(({ id, check, message }) => {
    const input = document.getElementById(id);
    if (!input) return;
    if (!check(input.value.trim())) {
      showFieldError(input, message);
      valid = false;
    } else {
      clearFieldError(input);
    }
  });
  return valid;
}

// Register form
const registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', (e) => {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const valid = validateForm(registerForm, [
      { id: 'name', check: (v) => v.length >= 2, message: 'Please enter your full name.' },
      { id: 'email', check: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), message: 'Please enter a valid email.' },
      { id: 'password', check: (v) => v.length >= 6, message: 'Password must be at least 6 characters.' },
      { id: 'confirm_password', check: () => password === confirm, message: 'Passwords do not match.' },
    ]);
    if (!valid) {
      e.preventDefault();
      const btn = registerForm.querySelector('button[type="submit"]');
      if (btn) { btn.classList.remove('loading'); btn.disabled = false; btn.textContent = 'Register'; }
    }
  });
}

// Login form
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', (e) => {
    const valid = validateForm(loginForm, [
      { id: 'email', check: (v) => v.length > 0, message: 'Please enter your email.' },
      { id: 'password', check: (v) => v.length > 0, message: 'Please enter your password.' },
    ]);
    if (!valid) {
      e.preventDefault();
      const btn = loginForm.querySelector('button[type="submit"]');
      if (btn) { btn.classList.remove('loading'); btn.disabled = false; btn.textContent = 'Login'; }
    }
  });
}

// Add / Edit student form
const studentForm = document.getElementById('studentForm');
if (studentForm) {
  studentForm.addEventListener('submit', (e) => {
    const valid = validateForm(studentForm, [
      { id: 'full_name', check: (v) => v.length >= 2, message: "Please enter the student's full name." },
      { id: 'email', check: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), message: 'Please enter a valid email.' },
      { id: 'phone', check: (v) => /^[0-9+\-\s]{7,20}$/.test(v), message: 'Please enter a valid phone number.' },
      { id: 'course', check: (v) => v.length > 0, message: 'Please enter a course name.' },
      { id: 'enrollment_date', check: (v) => v.length > 0, message: 'Please pick an enrollment date.' },
    ]);
    if (!valid) {
      e.preventDefault();
      const btn = studentForm.querySelector('button[type="submit"]');
      if (btn) { btn.classList.remove('loading'); btn.disabled = false; btn.textContent = btn.dataset.loadingText.includes('Updat') ? 'Update Student' : 'Save Student'; }
    }
  });
}
