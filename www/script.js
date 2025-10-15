let lastActivity = Date.now();
let reminderTimeout = null;

// Сброс таймера при активности
document.addEventListener('input', () => { lastActivity = Date.now(); resetReminder(); });
document.addEventListener('click', () => { lastActivity = Date.now(); resetReminder(); });

function resetReminder() {
  if (reminderTimeout) clearTimeout(reminderTimeout);
  reminderTimeout = setTimeout(checkInactivity, 15000);
}

function checkInactivity() {
  const now = Date.now();
  if (now - lastActivity >= 15000) {
    document.querySelectorAll('input, select').forEach(el => {
      el.classList.add('highlight');
      setTimeout(() => el.classList.remove('highlight'), 3000);
    });
  }
}

resetReminder();

// Только alert перед отправкой — форма ОТПРАВИТСЯ!
document.getElementById("doctorForm").addEventListener("submit", function() {
  const name = this.name.value;
  const age = this.age.value;
  const doctor = this.doctor.options[this.doctor.selectedIndex].text || this.doctor.value;
  const firstVisit = this.first_visit.checked ? "Да" : "Нет";
  const visitType = this.querySelector('input[name="visit_type"]:checked')?.value || "Не выбрано";

  alert(`Вы отправляете:\nИмя: ${name}\nВозраст: ${age}\nВрач: ${doctor}\nПервая консультация: ${firstVisit}\nФорма визита: ${visitType}`);
  // НЕТ preventDefault() → форма уйдёт в process.php
});
