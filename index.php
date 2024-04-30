<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product, .form-section {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .button {
            display: block;
            width: 200px;
            padding: 10px;
            margin: 20px auto;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, button {
            width: calc(100% - 22px);
            padding: 10px;
            margin-top: 5px;
        }
        #productList {
            display: none; /* Initialement masqué */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des produits</h1>

        <button class="button" onclick="toggleVisibility()">Afficher/Masquer les produits</button>
        <div id="productList" class="form-section">
            <?php
                // Charger et afficher les produits dès le chargement de la page
                $productsXml = file_get_contents('http://localhost:8080/product.management.web/api/products');
                $products = simplexml_load_string($productsXml);
                echo '<div>';
                foreach ($products->product as $product) {
                    echo '<div class="product">';
                    echo '<h2>' . htmlspecialchars($product->name) . '</h2>';
                    echo '<p>ID: ' . htmlspecialchars($product->id) . '</p>';
                    echo '<p>Prix: ' . htmlspecialchars($product->price) . '€</p>';
                    echo '<p>Description: ' . htmlspecialchars($product->description) . '</p>';
                    echo '</div>';
                }
                echo '</div>';
            ?>
            
        </div>

        <div class="form-section">
              <!-- Bouton pour dérouler le formulaire d'ajout -->
    <button class="button" onclick="toggleAddProductForm()">Ajouter un nouveau produit</button>

    <h2>Ajouter un nouveau produit</h2>
    <div id="addProductForm" class="form-section" style="display:none;">
        <form method="post">
            <label for="newProductId">Id du produit:</label>
            <input type="text" id="newProductId" name="newProductId" required>

            <label for="newProductName">Nom du produit:</label>
            <input type="text" id="newProductName" name="newProductName" required>

            <label for="newProductPrice">Prix:</label>
            <input type="number" id="newProductPrice" name="newProductPrice" step="0.01" required>

            <label for="newProductDescription">Description:</label>
            <textarea id="newProductDescription" name="newProductDescription" required></textarea>

            <label for="newProductCategory">Catégorie:</label>
            <input type="text" id="newProductCategory" name="newProductCategory" required>

            <label for="newProductStock">Stock:</label>
            <input type="number" id="newProductStock" name="newProductStock" required>

            <button class="button" type="submit" name="addProduct">Ajouter le produit</button>
        </form>
    </div>
    
  

    <?php
if (isset($_POST['addProduct'])) {
    $newProductId = $_POST['newProductId'];
    $newProductName = $_POST['newProductName'];
    $newProductPrice = $_POST['newProductPrice'];
    $newProductDescription = $_POST['newProductDescription'];
    $newProductCategory = $_POST['newProductCategory'];
    $newProductStock = $_POST['newProductStock'];

    // Préparez les données à envoyer en format XML
    $xmlData = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><product></product>');
    $xmlData->addChild('id', $newProductId);
    $xmlData->addChild('name', $newProductName);
    $xmlData->addChild('price', $newProductPrice);
    $xmlData->addChild('description', $newProductDescription);
    $xmlData->addChild('category', $newProductCategory);
    $xmlData->addChild('stock', $newProductStock);
    $postData = $xmlData->asXML();

    // URL de l'API pour ajouter un produit
    $url = 'http://localhost:8080/product.management.web/api/products/';

    // Initialiser cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/xml' // Modifiez ici pour application/xml
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
    // Exécutez la requête
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

   
if ($statusCode === 200) {
    // Stocker un message de succès en session pour l'afficher après la redirection
    session_start();
    $_SESSION['message'] = "Le produit avec l'ID " . htmlspecialchars($newProductName) . " a été ajouté avec succès.";
} else {
    // Stocker un message d'erreur en session
    session_start();
    $_SESSION['message'] = "Erreur lors de l'ajout du produit. Réponse du serveur : " . $statusCode;
}

// Redirection pour éviter la resoumission du formulaire
header("Location: index.php");
}

?>

</div>

<div class="form-section">
    <h2>Supprimer un produit par ID</h2>
    <form method="post">
        <label for="productIdToDelete">ID du produit à supprimer:</label>
        <input type="text" id="productIdToDelete" name="productIdToDelete" required>
        <button class="button" type="submit" name="deleteProduct">Supprimer le produit</button>
    </form>
</div>


<?php
if (isset($_POST['deleteProduct'])) {
    $deleteProductId = $_POST['productIdToDelete'];

    // URL de l'API pour supprimer un produit avec l'ID spécifié
    $url = 'http://localhost:8080/product.management.web/api/products/' . $deleteProductId;

    // Initialiser cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/xml'  // Spécifier le format XML si nécessaire
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    // Exécutez la requête
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode === 200) {
        // Stocker un message de succès en session pour l'afficher après la redirection
        session_start();
        $_SESSION['message'] = "Le produit avec l'ID " . htmlspecialchars($deleteProductId) . " a été supprimé avec succès.";
    } else {
        // Stocker un message d'erreur en session
        session_start();
        $_SESSION['message'] = "Erreur lors de la suppression du produit. Réponse du serveur : " . $statusCode;
    }

    // Redirection pour éviter la resoumission du formulaire
    header("Location: index.php"); // Assurez-vous de remplacer 'votre_page.php' par le nom de votre page
    exit();
}
?>      


        </div>

        <div class="form-section">
    <button class="button" onclick="toggleUpdateForm()">Mettre à jour un produit</button>
    <div id="updateProductForm" style="display:none;">
        <h2>Mettre à jour un produit</h2>
        <form method="post">
            <label for="updateProductId">ID du produit :</label>
            <input type="text" id="updateProductId" name="updateProductId" required>

            <label for="updateProductName">Nom :</label>
            <input type="text" id="updateProductName" name="updateProductName" required>

            <label for="updateProductPrice">Prix :</label>
            <input type="number" id="updateProductPrice" name="updateProductPrice" step="0.01" required>

            <label for="updateProductDescription">Description :</label>
            <textarea id="updateProductDescription" name="updateProductDescription" required></textarea>

            <label for="updateProductCategory">Catégorie :</label>
            <input type="text" id="updateProductCategory" name="updateProductCategory" required>

            <label for="updateProductStock">Stock :</label>
            <input type="number" id="updateProductStock" name="updateProductStock" required>

            <button class="button" type="submit" name="updateProduct">Mettre à jour</button>
        </form>

        
    </div>

    <?php
// Assurez-vous que vous lisez les données PUT correctement
parse_str(file_get_contents("php://input"), $_PUT);

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $productName = $_PUT['updateProductName'];
    $productPrice = $_PUT['updateProductPrice'];
    $productDescription = $_PUT['updateProductDescription'];
    $productCategory = $_PUT['updateProductCategory'];
    $productStock = $_PUT['updateProductStock'];

    // Connectez-vous à la base de données et mettez à jour le produit
    // Répondez selon le résultat de la mise à jour
    echo "Produit mis à jour"; // ou gestion des erreurs
}
?>

    
</div>

        <script>
            function toggleVisibility() {
                var list = document.getElementById('productList');
                list.style.display = (list.style.display === 'none') ? 'block' : 'none';
            }

            // Fonction pour dérouler/rouler le formulaire d'ajout de produit
        function toggleAddProductForm() {
            var form = document.getElementById('addProductForm');
            form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        }

        
            function toggleUpdateForm() {
                var form = document.getElementById('updateProductForm');
                form.style.display = (form.style.display === 'none') ? 'block' : 'none';
            }


        </script>
    </div>

 
    

 





