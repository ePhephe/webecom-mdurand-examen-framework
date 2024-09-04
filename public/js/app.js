/**
 * Action de suppression (colaborateurs et restaurant)
 */
// On récupère le lien de suppression
const btnDelete = document.getElementById(`button_suppresion`);
// Si il est bien présent
if(btnDelete) {
    // On met en place l'écouteur d'évènements
    btnDelete.addEventListener(`click`, (e) => {
        // On arrête le fonctionnement par défaut
        e.preventDefault();
        // On appel l'URL avec la méthode DELETE et le CSRF Token
        fetch(e.target.href, {
            method: 'DELETE',
            body: JSON.stringify({ _token: csrfToken })
        })
        .then(response => { return response.json(); })
        .then(data => {
            // Si c'est bon, on retourne à la page de liste
            if(data.status === 'success') {
                window.location = data.redirect;
            }
            // Sinon on recharge la page
            else {
                window.location.reload();
            }
            console.log('Réponse :', data);
        })
        .catch(error => {
            console.error('Erreur :', error);
        });
    });
}

/**
 * Gère la mise en place du timeout sur les messages flash
 */
function setTimeoutFlash(){
    let divModalFlash = document.getElementById(`modalFlash`);
    let arrayDivMessages = divModalFlash.querySelectorAll(`div`);

    arrayDivMessages.forEach(message => {
        setTimeout(deleteFlash,5000, divModalFlash,message);
    });
}

/**
 * Supprime les messages flash
 */
function deleteFlash(divModalFlash,message){
    //On masque les messages avec le display none (classe CSS d-none)
    divModalFlash.remove(message);
}

// Lance le traitement des messages flash
setTimeoutFlash();