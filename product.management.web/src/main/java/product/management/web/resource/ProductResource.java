package product.management.web.resource;

import product.management.web.data.Product;
import product.management.web.service.ProductService;

import java.util.ArrayList;
import java.util.Collection;
import java.util.List;

import javax.ws.rs.*;
import javax.ws.rs.core.*;

@Path("/products")
public class ProductResource {
    private ProductService productService = new ProductService();

    @Context
    UriInfo uriInfo;

    @POST
    @Consumes(MediaType.APPLICATION_XML)
    @Produces(MediaType.APPLICATION_XML)
    public Response addProduct(Product product) {
        Product newProduct = productService.addProduct(product);
        if (newProduct == null) {
            return Response.status(Response.Status.BAD_REQUEST).build();
        }
        UriBuilder builder = uriInfo.getAbsolutePathBuilder();
        builder.path(Integer.toString(newProduct.getId()));
        return Response.created(builder.build()).entity(newProduct).build();
    }

    @GET
    @Produces(MediaType.APPLICATION_XML)
    public List<Product> getAllProducts() {
        return productService.getAllProducts();
        
    }
    @GET
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_XML)
    public Response getProduct(@PathParam("id") int id) {
        Product product = productService.getProduct(id);
        if (product == null) {
            return Response.status(Response.Status.NOT_FOUND).build();
        }
        Link link = Link.fromUri(uriInfo.getRequestUri()).rel("self").type("application/xml").build();
        return Response.ok(product).links(link).build();
    }

    @PUT
    @Path("/{id}")
    @Consumes(MediaType.APPLICATION_XML)
    @Produces(MediaType.APPLICATION_XML)
    public Response updateProduct(@PathParam("id") int id, Product product) {
        Product updatedProduct = productService.updateProduct(id, product);
        if (updatedProduct == null) {
            return Response.status(Response.Status.NOT_FOUND).build();
        }
        return Response.ok(updatedProduct).build();
    }

    @DELETE
    @Path("/{id}")
    @Produces(MediaType.APPLICATION_XML)
    public Response deleteProduct(@PathParam("id") int id) {
        boolean deleted = productService.deleteProduct(id);
        if (!deleted) {
            return Response.status(Response.Status.NOT_FOUND).build();
        }
        return Response.ok().build();
    }
}
