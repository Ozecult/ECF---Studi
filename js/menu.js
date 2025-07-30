document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments
    const boutonMenu = document.getElementById('menu-deroulant');
    const menuProfile = document.getElementById('menu-profile');
    const menuNavigation = document.getElementById('mobile-nav-menu');
    
    // Vérification que les éléments existent
    if (!boutonMenu || !menuProfile || !menuNavigation) {
        console.error('Éléments du menu non trouvés');
        return;
    }
    
    // Fonction pour fermer tous les menus
    function fermerTousLesMenus() {
        menuProfile.classList.remove('show');
        menuNavigation.classList.remove('show');
        menuProfile.style.display = 'none';
        menuNavigation.style.display = 'none';
        boutonMenu.setAttribute('aria-expanded', 'false');
    }
    
    // Fonction pour afficher le menu navigation
    function afficherMenuNavigation() {
        fermerTousLesMenus();
        menuNavigation.classList.add('show');
        menuNavigation.style.display = 'block';
        boutonMenu.setAttribute('aria-expanded', 'true');
    }
    
    // Fonction pour afficher le menu profil
    function afficherMenuProfile() {
        fermerTousLesMenus();
        menuProfile.classList.add('show');
        menuProfile.style.display = 'block';
        boutonMenu.setAttribute('aria-expanded', 'true');
    }
    
    // Événement clic sur le bouton
    boutonMenu.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Détecter quelle icône a été cliquée
        const clickedElement = e.target;
        
        if (clickedElement.classList.contains('burger-icon') || 
            (clickedElement.textContent && clickedElement.textContent.trim() === 'menu')) {
            // Clic sur l'icône burger (menu)
            if (menuNavigation.style.display === 'block') {
                fermerTousLesMenus();
            } else {
                afficherMenuNavigation();
            }
        } else if (clickedElement.classList.contains('material-symbols-outlined') && 
                  clickedElement.getAttribute('aria-label') === 'menu-deroulant') {
            // Clic sur l'icône profil (account_circle)
            if (menuProfile.style.display === 'block') {
                fermerTousLesMenus();
            } else {
                afficherMenuProfile();
            }
        } else {
            // Clic sur le bouton mais pas sur une icône spécifique
            // Par défaut, on alterne entre les deux menus ou on ferme
            if (menuProfile.style.display === 'block' || menuNavigation.style.display === 'block') {
                fermerTousLesMenus();
            } else {
                // Afficher le menu profil par défaut
                afficherMenuProfile();
            }
        }
    });
    
    // Fermer les menus si on clique ailleurs sur la page
    document.addEventListener('click', function(e) {
        if (!boutonMenu.contains(e.target) && 
            !menuProfile.contains(e.target) && 
            !menuNavigation.contains(e.target)) {
            fermerTousLesMenus();
        }
    });
    
    // Fermer les menus avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fermerTousLesMenus();
        }
    });
    
    // Gestion de l'accessibilité avec les flèches
    boutonMenu.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            // Par défaut, afficher le menu profil
            afficherMenuProfile();
            
            // Focus sur le premier lien
            const premierLien = menuProfile.querySelector('a');
            if (premierLien) {
                premierLien.focus();
            }
        }
    });
    
    // Navigation dans les menus avec les flèches
    [menuProfile, menuNavigation].forEach(menu => {
        menu.addEventListener('keydown', function(e) {
            const liens = menu.querySelectorAll('a');
            const index = Array.from(liens).indexOf(document.activeElement);
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = (index + 1) % liens.length;
                liens[nextIndex].focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = index > 0 ? index - 1 : liens.length - 1;
                liens[prevIndex].focus();
            } else if (e.key === 'Escape') {
                fermerTousLesMenus();
                boutonMenu.focus();
            }
        });
    });
    
    // Fermer les menus quand on clique sur un lien
    document.querySelectorAll('#menu-profile a, #mobile-nav-menu a').forEach(link => {
        link.addEventListener('click', function() {
            fermerTousLesMenus();
        });
    });
    
    // Initialisation : s'assurer que les menus sont masqués au chargement
    fermerTousLesMenus();
    
    // Fonction pour détecter si on est en mobile (768px)
    function estEnModeMobile() {
        return window.innerWidth <= 768;
    }
    
    // Réajuster les menus si on redimensionne la fenêtre
    window.addEventListener('resize', function() {
        if (!estEnModeMobile()) {
            fermerTousLesMenus();
        }
    });
});