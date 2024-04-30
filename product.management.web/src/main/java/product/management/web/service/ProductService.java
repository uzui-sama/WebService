package product.management.web.service;

import product.management.web.data.Product;

import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;

public class ProductService {
    private static final ConcurrentHashMap<Integer, Product> products = new ConcurrentHashMap<>();
    private static final AtomicInteger idCounter = new AtomicInteger();

   
 


  

    public Product addProduct(Product product) {
        int newId = idCounter.incrementAndGet();
        product.setId(newId);
        products.put(newId, product);
        return product;
    }

    public Product getProduct(int id) {
        return products.get(id);
    }

    public Product updateProduct(int id, Product updatedProduct) {
        if (products.containsKey(id)) {
            updatedProduct.setId(id);
            products.replace(id, updatedProduct);
            return updatedProduct;
        }
        return null;
    }

    public boolean deleteProduct(int id) {
        return products.remove(id)!= null;
    }
   
    public List<Product> getAllProducts() {
        return new ArrayList<>(products.values());
    }
   
    
}