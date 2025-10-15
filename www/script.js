let lastActivity = Date.now();
let reminderTimeout = null;

// Сброс таймера при активности
document.addEventListener('input', () => {
  lastActivity = Date.now();
  resetReminder();
});

document.addEventListener('click', () => {
  lastActivity = Date.now();
  resetReminder();
});

function resetReminder() {
  if (reminderTimeout) clearTimeout(reminderTimeout);
  reminderTimeout = setTimeout(checkInactivity, 15000); // 15 сек
}

function checkInactivity() {
  const now = Date.now();
  if (now - lastActivity >= 15000) {
    // Подсветка всех полей формы
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(el => {
      el.classList.add('highlight');
      setTimeout(() => el.classList.remove('highlight'), 3000);
    });
  }
}

// Запуск таймера
resetReminder();

// Обработка формы
document.getElementById("doctorForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  let output = "<h2>Ваша запись:</h2>";

  const labels = {
    name: "Имя",
    age: "Возраст",
    doctor: "Врач",
    visit_type: "Форма визита"
  };

  const doctorMap = {
    therapist: "Терапевт",
    dentist: "Стоматолог",
    cardiologist: "Кардиолог",
    dermatologist: "Дерматолог"
  };

  const visitTypeMap = {
    "on-site": "Очно",
    online: "Онлайн"
  };

  for (const [key, value] of formData.entries()) {
    if (key === "first_visit") {
      output += `<p><b>Первая консультация:</b> Да</p>`;
    } else if (key === "doctor") {
      output += `<p><b>${labels[key]}:</b> ${doctorMap[value]}</p>`;
    } else if (key === "visit_type") {
      output += `<p><b>${labels[key]}:</b> ${visitTypeMap[value]}</p>`;
    } else {
      output += `<p><b>${labels[key] || key}:</b> ${value}</p>`;
    }
  }

  if (!formData.has("first_visit")) {
    output += `<p><b>Первая консультация:</b> Нет</p>`;
  }

  document.getElementById("result").innerHTML = output;
  document.getElementById("result").style.display = "block";

  // Сброс таймера после отправки
  resetReminder();
});
