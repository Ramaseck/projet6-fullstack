<?php
// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "mabase");

// Vérifier la connexion
if ($mysqli->connect_error) {
    die("La connexion à la base de données a échoué : " . $mysqli->connect_error);
}

// Opération 1 : Enregistrer les produits stockés dans la base de données
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ajouter_produit"])) {
    $nomProduit = $_POST["nom_produit"];
    $quantiteStock = $_POST["quantite_stock"];

    $sql = "INSERT INTO produits (nom_produit, quantite_stock) VALUES ('$nomProduit', $quantiteStock)";

    if ($mysqli->query($sql) === TRUE) {
        echo "Produit ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout du produit : " . $mysqli->error;
    }
}

// Opération 2 : Enregistrer les produits pris du stock
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["prendre_produit"])) {
    $nomProduit = $_POST["nom_produit"];
    $quantitePrise = $_POST["quantite_prise"];

    $result = $mysqli->query("SELECT quantite_stock FROM produits WHERE nom_produit = '$nomProduit'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $quantiteStock = $row["quantite_stock"];

        if ($quantitePrise <= $quantiteStock) {
            $nouvelleQuantite = $quantiteStock - $quantitePrise;
            $mysqli->query("UPDATE produits SET quantite_stock = $nouvelleQuantite WHERE nom_produit = '$nomProduit'");
            echo "Produit pris du stock avec succès.";
        } else {
            echo "Quantité prise supérieure à la quantité en stock.";
        }
    } else {
        echo "Le produit n'existe pas dans la base de données.";
    }
}

// Opération 3 : Vérifier la quantité des produits disponibles en stock
$result = $mysqli->query("SELECT * FROM produits");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion de Stock</title>
</head>
<body>
    <h2>Enregistrer un produit en stock</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="text" name="nom_produit" placeholder="Nom du produit" required>
        <input type="number" name="quantite_stock" placeholder="Quantité en stock" required>
        <input type="submit" name="ajouter_produit" value="Ajouter au stock">
    </form>

    <h2>Prendre un produit du stock</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="text" name="nom_produit" placeholder="Nom du produit" required>
        <input type="number" name="quantite_prise" placeholder="Quantité à prendre" required>
        <input type="submit" name="prendre_produit" value="Prendre du stock">
    </form>

    <h2>Verifier Stock de produits</h2>
    <table border="1">
        <tr>
            <th>Nom du produit</th>
            <th>Quantité en stock</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["nom_produit"] . "</td>";
                echo "<td>" . $row["quantite_stock"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Aucun produit enregistré dans la base de données.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Fermer la connexion
$mysqli->close();
?>
