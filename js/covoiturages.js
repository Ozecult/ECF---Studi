// Gestion du bouton toggle des filtres
document.addEventListener('DOMContentLoaded', function() {
    const filtresToggle = document.querySelector('.filtres-toggle');
    const filtresContent = document.querySelector('.filtres-content');
    const resetButton = document.querySelector('.filtres button[type="reset"]');
    const formFiltres = document.querySelector('.form-filtres');

    // Toggle des filtres en responsive
    if (filtresToggle && filtresContent) {
        filtresToggle.addEventListener('click', function() {
            filtresContent.classList.toggle('hidden');
            
            // Changer le texte du bouton selon l'état
            if (filtresContent.classList.contains('hidden')) {
                filtresToggle.textContent = '☰ Filtres';
            } else {
                filtresToggle.textContent = '✕ Masquer les filtres';
            }
        });

        // Masquer les filtres par défaut sur mobile
        function checkScreenSize() {
            if (window.innerWidth <= 1024) {
                filtresContent.classList.add('hidden');
                filtresToggle.textContent = '☰ Filtres';
            } else {
                filtresContent.classList.remove('hidden');
            }
        }

        // Vérifier la taille d'écran au chargement et au redimensionnement
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
    }

    // Gestion du bouton "Effacer les filtres"
    if (resetButton && formFiltres) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Réinitialiser toutes les checkboxes
            const checkboxes = formFiltres.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Réinitialiser les range sliders
            const rangeInputs = formFiltres.querySelectorAll('input[type="range"]');
            rangeInputs.forEach(range => {
                // Pour le prix en crédits, remettre à 0 (minimum)
                if (range.closest('.price')) {
                    range.value = range.min || 0;
                } else {
                    // Pour les autres (comme le temps), remettre au milieu
                    const min = parseFloat(range.min) || 0;
                    const max = parseFloat(range.max) || 100;
                    range.value = (min + max) / 2;
                }
                
                // Déclencher l'événement pour mettre à jour l'affichage
                range.dispatchEvent(new Event('input'));
            });
            
            // Réinitialiser les étoiles
            const etoiles = formFiltres.querySelectorAll('.etoiles .material-symbols-outlined');
            etoiles.forEach(etoile => {
                etoile.textContent = 'star_border'; // Étoile vide
                etoile.style.color = 'var(--vert-fonce)';
            });
            
            console.log('Filtres réinitialisés');
        });
    }

    // Bonus : Gestion des range sliders pour afficher la valeur en temps réel
    const rangeInputs = document.querySelectorAll('.time input[type="range"], .price input[type="range"]');
    rangeInputs.forEach(range => {
        const container = range.closest('.time, .price');
        const valueDisplay = container.querySelector('span');
        
        if (valueDisplay) {
            // Fonction pour mettre à jour l'affichage
            function updateDisplay() {
                let value = range.value;
                
                // Si c'est un slider de temps, formatter en heures:minutes
                if (container.classList.contains('time')) {
                    const hours = Math.floor(value / 60);
                    const minutes = value % 60;
                    valueDisplay.textContent = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
                } 
                // Si c'est un slider de prix, afficher en crédits (pas de texte pour 0)
                else if (container.classList.contains('price')) {
                    // Limiter l'affichage à 50 même si le max est 100
                    const displayValue = Math.min(value, 50);
                    if (displayValue == 0) {
                        valueDisplay.textContent = '0';
                    } else {
                        valueDisplay.textContent = displayValue + (displayValue == 1 ? ' crédit' : ' crédits');
                    }
                }
            }
            
            // Mettre à jour au chargement
            updateDisplay();
            
            // Mettre à jour lors du changement
            range.addEventListener('input', updateDisplay);
        }
    });

    // Bonus : Gestion des étoiles (système de notation cliquable)
    const etoilesContainer = document.querySelector('.etoiles');
    if (etoilesContainer) {
        const etoiles = etoilesContainer.querySelectorAll('.material-symbols-outlined');
        
        // Initialiser toutes les étoiles comme vides au démarrage
        etoiles.forEach(etoile => {
            etoile.textContent = 'star_border';
            etoile.style.color = 'var(--vert-fonce)';
        });
        
        etoiles.forEach((etoile, index) => {
            etoile.addEventListener('click', function() {
                // Retirer la sélection de toutes les étoiles
                etoiles.forEach(e => {
                    e.textContent = 'star_border'; // Étoile vide
                    e.style.color = 'var(--vert-fonce)';
                });
                
                // Sélectionner les étoiles jusqu'à celle cliquée
                for (let i = 0; i <= index; i++) {
                    etoiles[i].textContent = 'star_border';
                    etoiles[i].style.color = 'var(--vert-clair)';
                }
                
                console.log(`${index + 1} étoile(s) sélectionnée(s)`);
            });
        });
    }
});