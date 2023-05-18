function renderEquations() {
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, document.getElementById('task')]);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, document.getElementById('solution')]);
}