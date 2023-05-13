function loadLanguageFile(language) {    
    return import(`./${language}.js`);
}
document.addEventListener('DOMContentLoaded', () => {
    const language = navigator.language.slice(0, 2);
    loadLanguageFile(language)
        .then(translations => updateText(translations.default));
});
function updateText(translations) {
    const elements = document.querySelectorAll('[data-translate]');
    elements.forEach(element => {
        const key = element.getAttribute('data-translate');
        if (translations[key]) {
            element.textContent = translations[key];
        }
    });
}
$('.languageSwitcher').click( button => {
    const language = button.target.getAttribute('data-language');
    loadLanguageFile(language)
        .then(translations => updateText(translations.default));
});
