
# Používať oficiálny image PHP s Apache
FROM php



# Skopírovať súbory z aktuálneho adresára na vášom počítači do kontajnera
COPY ./index.php ./
COPY ./print.php ./
COPY ./fpdf/fpdf.php ./fpdf/
COPY ./api/config.php ./api/
COPY ./api/login.php ./api/
COPY ./api/logout.php ./api/
COPY ./api/register.php ./api/
COPY ./api/equationFunctionionality.php ./api/
COPY ./views/registration.php ./views/
COPY ./views/showStudents.php ./views/
COPY ./views/studentHome.php ./views/
COPY ./views/teacher.php ./views/
COPY ./views/equations.php ./views/
COPY ./languages/en.js ./languages/
COPY ./languages/sk.js ./languages/
COPY ./templates/navbar.php ./templates/
COPY ./languages/languageSwitching.js ./languages/
COPY ./styles/all.css ./styles/
COPY ./equations/images/blokovka01_00002.jpg ./images/equations/
COPY ./equations/latex/blokovka01pr.tex ./latex/equations/



EXPOSE 4000
CMD [ "php", "-S", "0.0.0.0:4000" ]