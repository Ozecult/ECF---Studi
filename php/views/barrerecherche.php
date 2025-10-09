<?php
$phpSelf = $_SERVER['PHP_SELF'];
$basePath = dirname($phpSelf);
?>

<section class="recherche">
        <h2>Rechercher un covoiturage</h2>
        <form action="<?= $basePath ?>/index.php" method="get" autocomplete="on">
          <!-- Départ -->
          <div class="choix">
            <label for="depart">
              <span
                class="material-symbols-outlined"
                aria-label="Départ"
                role="img"
                >home_pin</span
              >
              <span>Départ</span>
            </label>
            <input type="text" name="depart" id="depart" required />
            <div class="suggestions" id="suggestions-depart"></div>
          </div>

          <!-- Destination -->
          <div class="choix">
            <label for="destination">
              <span
                class="material-symbols-outlined"
                aria-label="Destination"
                role="img"
                >location_on</span
              >
              <span>Destination</span>
            </label>
            <input type="text" name="destination" id="destination" required />
            <div class="suggestions" id="suggestions-destination"></div>
          </div>
          <!-- Date -->
          <div class="choix calendar" data-field="date">
            <label for="date">
              <span
                class="material-symbols-outlined"
                aria-label="Calendrier"
                role="img"
                >calendar_month</span
              >
            </label>
            <input type="text" name="date" id="date" required />
          </div>

          <!-- Passagers -->
          <div class="choix" data-field="passagers">
            <label for="passagers" class="form-label">
              <span
                class="material-symbols-outlined"
                aria-label="passager"
                role="img"
                >group</span
              >
              <span class="visually-hidden">Passagers</span>
            </label>
            <select name="passagers" id="passagers">
              <option value="1" selected>1 passager</option>
              <option value="2">2 passagers</option>
              <option value="3">3 passagers</option>
              <option value="4">4 passagers</option>
              <option value="5">5 passagers</option>
              <option value="6">6 passagers</option>
              <option value="7">7 passagers</option>
              <option value="8">8 passagers</option>
            </select>
          </div>

          <!-- Champ caché pour la page -->
          <input type="hidden" name="page" value="covoiturages">
        
          <!-- Bouton -->
          <button id="cherche" type="submit">Rechercher</button>
        </form>
      </section>
