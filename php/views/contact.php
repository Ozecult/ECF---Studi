<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="EcoRide facilite vos trajets en covoiturage avec une approche responsable, humaine et économique. Rejoignez une communauté engagée pour la planète !"
    />
    <title>EcoRide - Contact</title>
    <link rel="stylesheet" href="/ecoride/css/style.css" />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="/ecoride/js/script.js"></script>
  </head>
  <body>
    <!-- HEADER -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- MAIN -->
    <main id="contenu" class="contact">
      <h1>Contactez-nous</h1>
      <form
        class="form-contact card"
        id="formContact"
        method="post"
        autocomplete="on"
      >
        <fieldset>
          <legend>Formulaire</legend>
          <!-- email -->
          <div class="form-group card">
            <label for="email"
              ><span
                class="material-symbols-outlined"
                aria-label="Email"
                role="img"
                >email</span
              >Adresse email</label
            >
            <input type="email" name="email" id="email" required />
            <div
              id="error-email"
              class="error-text"
              style="display: none"
            ></div>
          </div>

          <!-- Téléphone -->
          <div class="form-group card">
            <label for="tel"
              ><span
                class="material-symbols-outlined"
                aria-label="Téléphone"
                role="img"
                >phone</span
              >Numéro de téléphone</label
            >
            <input
              type="tel"
              name="tel"
              id="tel"
              required
              placeholder="06 00 00 00 00"
            />
          </div>

          <!-- Prénom -->
          <div class="form-group card">
            <label for="prenom"
              ><span
                class="material-symbols-outlined"
                aria-label="Prénom"
                role="img"
                >person</span
              >Prénom</label
            >
            <input type="text" name="prenom" id="prenom" required />
          </div>

          <!-- Nom -->
          <div class="form-group card">
            <label for="nom"
              ><span
                class="material-symbols-outlined"
                aria-label="Nom"
                role="img"
                >badge</span
              >Nom de famille</label
            >
            <input type="text" name="nom" id="nom" required />
          </div>

          <!-- Sujet -->
          <div class="form-group card">
            <label for="sujet"
              ><span
                class="material-symbols-outlined"
                aria-label="Sujet"
                role="img"
                >topic</span
              >Sujet</label
            >
            <select name="sujet" id="sujet" required>
              <option value="">-- aucune sélection --</option>
              <option value="question">Question générale</option>
              <option value="compte">Problème de compte</option>
              <option value="trajet">Problème avec un covoiturage</option>
              <option value="conducteur">Signaler un conducteur</option>
              <option value="passager">Signaler un passager</option>
              <option value="avis">Demande de suppression d’avis</option>
              <option value="bug">Bug ou problème technique</option>
              <option value="autre">Autre demande</option>
            </select>
          </div>

          <!-- Champ texte -->
          <div class="form-group card">
            <label for="champtext"
              ><span
                class="material-symbols-outlined"
                aria-label="Description"
                role="img"
                >description</span
              >Description</label
            >
            <textarea
              id="champtext"
              name="champtext"
              rows="5"
              maxlength="800"
              placeholder="Veuillez indiquer ici l'objet de votre demande"
            ></textarea>
          </div>

          <!-- Bouton soumettre -->
          <div class="button card">
            <button class="button" type="submit">Soumettre</button>
          </div>
        </fieldset>
      </form>
    </main>
    <!-- FOOTER -->
    <?php require __DIR__ . '/footer.php'; ?>
    <!-- Script formulaire contact -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formContact');
        
        form.addEventListener('submit', async function(e) {
          e.preventDefault();
          
          const formData = new FormData(form);
          
          try {
            const response = await fetch('/ecoride/php/api/api-router.php?action=send-contact', {
              method: 'POST',
              body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
              alert('✅ ' + data.message);
              form.reset();
            } else {
              alert('❌ ' + data.message);
            }
            
          } catch (error) {
            console.error('Erreur:', error);
            alert('❌ Erreur lors de l\'envoi du message');
          }
        });
      });
      </script>
  </body>
</html>
