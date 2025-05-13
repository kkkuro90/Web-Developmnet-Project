document.addEventListener('DOMContentLoaded', function() {
    // Переключение между разделами профиля
    const menuLinks = document.querySelectorAll('.profile-menu a');
    const profileSections = document.querySelectorAll('.profile-section');
    
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Удаляем активный класс у всех ссылок и разделов
            menuLinks.forEach(item => item.classList.remove('active'));
            profileSections.forEach(section => section.classList.remove('active'));
            
            // Добавляем активный класс к текущей ссылке
            this.classList.add('active');
            
            // Показываем соответствующий раздел
            const targetSection = document.querySelector(this.getAttribute('href'));
            targetSection.classList.add('active');
        });
    });
    
    // Валидация формы изменения пароля
    const passwordForm = document.querySelector('.settings-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Новый пароль и подтверждение пароля не совпадают!');
            }
        });
    }
});