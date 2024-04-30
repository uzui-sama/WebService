package product.management.web.client;

import javax.ws.rs.NotFoundException;
import javax.ws.rs.core.*;
import org.apache.cxf.jaxrs.client.WebClient;
import product.management.web.data.Product;

import java.util.List;

public class ProductClient {

    private static final String webServiceUrl = "http://localhost:8080/product.management.web/api/products";

    public static void main(String[] args) {
        // Créez un produit Laptop
        Product laptop = new Product(2, "Laptop", 999.99, "High performance laptop", "Electronics", 50);
        // Ajoutez le produit Laptop et récupérez son ID
        Integer laptopId = addProduct(laptop);
        // Vérifiez si l'ajout du produit Laptop a réussi
        if (laptopId != null) {
            // Si oui, récupérez et affichez les détails du produit Laptop
            getProduct(laptopId);
        } else {
            // Sinon, affichez un message d'erreur
            System.out.println("Failed to add Laptop, cannot fetch it.");
        }

        // Créez un produit Smartphone
        Product smartphone = new Product(1, "Smartphone", 499.99, "Latest model smartphone", "Electronics", 150);
        // Ajoutez le produit Smartphone et récupérez son ID
        Integer smartphoneId = addProduct(smartphone);
        // Vérifiez si l'ajout du produit Smartphone a réussi
        if (smartphoneId != null) {
            // Si oui, récupérez et affichez les détails du produit Smartphone
            getProduct(smartphoneId);
        } else {
            // Sinon, affichez un message d'erreur
            System.out.println("Failed to add Smartphone, cannot fetch it.");
        }
    }


    private static void getProduct(Integer id) {
        if (id == null) {
            System.out.println("Product ID is null, cannot perform get operation.");
            return;
        }
        System.out.print("Getting product " + id + "... ");
        WebClient client = WebClient.create(webServiceUrl).path(id.toString());
        try {
            Product product = client.get(Product.class);
            System.out.println(product.toString());
        } catch (NotFoundException e) {
            System.out.println("Product not found.");
        } catch (Exception e) {
            System.out.println("Error retrieving product: " + e.getMessage());
        }
    }


    private static Integer addProduct(Product product) {
        System.out.print("Adding " + product.getName() + "... ");
        
        // Créez un WebClient avec l'URL du service web
        WebClient client = WebClient.create(webServiceUrl);
        
        // Spécifie le type de contenu de la requête
        client.type(MediaType.APPLICATION_XML);
        
        // Envoie la requête POST avec le produit en tant qu'entité
        Response response = client.post(product);
        
        // Vérifie la réponse
        if (response.getStatus() == Response.Status.BAD_REQUEST.getStatusCode() || response.getStatus() != Response.Status.CREATED.getStatusCode()) {
            System.out.println("Failed to add product. Status: " + response.getStatus());
            return null;
        }
        
        // Récupère l'en-tête "Location" contenant l'URL du produit créé
        String location = response.getHeaderString("Location");
        if (location == null) {
            System.out.println("Location header is missing.");
            return null;
        }
        
        // Extrait l'ID du produit à partir de l'URL
        try {
            return Integer.parseInt(location.substring(location.lastIndexOf('/') + 1));
        } catch (NumberFormatException e) {
            System.out.println("Failed to parse product ID from location URI: " + e.getMessage());
            return null;
        }
    }


    private static void deleteProduct(Integer id) {
        System.out.print("Deleting product " + id + "... ");
        WebClient client = WebClient.create(webServiceUrl).path(id.toString());
        Response response = client.delete();
        if (response.getStatus() == Response.Status.OK.getStatusCode()) {
            System.out.println("Product deleted successfully.");
        } else {
            System.out.println("Failed to delete product. Status: " + response.getStatus());
        } 
    }



    private static void getAllProducts() {
        System.out.println("Getting all products...");
        
        // Créez un WebClient avec l'URL du service web
        WebClient client = WebClient.create(webServiceUrl);
        
        // Spécifie le type de contenu attendu dans la réponse
        client.accept(MediaType.APPLICATION_XML);
        
        try {
            // Envoie une requête GET pour récupérer tous les produits
            List<Product> products = client.get(new GenericType<List<Product>>() {});
            
            // Affiche les détails de chaque produit
            for (Product product : products) {
                System.out.println(product.toString());
            }
        } catch (Exception e) {
            // Gère les erreurs d'accès au service web
            System.out.println("Failed to retrieve products: " + e.getMessage());
        }
    }

    private static void updateProduct(Integer id, Product product) {
        System.out.print("Updating product " + id + "... ");
        WebClient client = WebClient.create(webServiceUrl).path(id.toString());
        client.type(MediaType.APPLICATION_XML);
        Response response = client.put(product);
        if (response.getStatus() == Response.Status.OK.getStatusCode()) {
            System.out.println("Product updated successfully.");
        } else {
            System.out.println("Update failed. Status: " + response.getStatus());
        }
    }
}
