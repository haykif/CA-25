<html lang="fr">

<?php
require_once "database.php";
$query = "SELECT * FROM Acces_log";
$stmt  = $pdo->query($query);
?>

<!DOCTYPE html>
<h2>Dernières Activités</h2>
    <table>
      <thead>
        <tr>
          <th>ID Accès</th>
          <th>Date Heure Entrée</th>
          <th>Date Heure Sortie</th>
          <th>ID User</th>
        </tr>
      </thead>
      <tbody id="activity-log">
        <?php
        // Boucle pour afficher chaque ligne de résultat dans le tableau
        while ($row = $stmt->fetch()) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($row['idAcces']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date_heure_entree']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date_heure_sortie']) . "</td>";
            echo "<td>" . htmlspecialchars($row['IdUser']) . "</td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
      
      </html>