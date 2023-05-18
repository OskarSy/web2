var language;
function loadLanguageFile(language) {
    return import(`./${language}.js`);
}
document.addEventListener('DOMContentLoaded', () => {
    language = navigator.language.slice(0, 2);
    loadLanguageFile(language)
        .then(translations => updateText(translations.default));
});
//============================update Stranky preklady
let observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
        if (mutation.addedNodes.length) {            
            loadLanguageFile(language)
                .then(translations => updateText(translations.default));
        }
    })
});
let wrapper = document.getElementById('wrapper');
observer.observe(wrapper, {
    childList: true,
    subtree: true
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
$('.languageSwitcher').click(button => {
    language = button.target.getAttribute('data-language');
    loadLanguageFile(language)
        .then(translations => updateText(translations.default));
});