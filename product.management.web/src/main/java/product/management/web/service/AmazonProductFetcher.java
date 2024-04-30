package product.management.web.service;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;

public class AmazonProductFetcher {
    public String fetchProducts() {
        String url = "https://real-time-amazon-data.p.rapidapi.com/search?query=Phone&page=1&country=US&category_id=aps";
        CloseableHttpClient httpClient = HttpClients.createDefault();
        try {
            HttpGet request = new HttpGet(url);
            request.addHeader("X-RapidAPI-Key", "79d4200bcemshde033f1711c8f03p1a6357jsn0d24cc3c1130");
            request.addHeader("X-RapidAPI-Host", "real-time-amazon-data.p.rapidapi.com");
            return EntityUtils.toString(httpClient.execute(request).getEntity());
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        } finally {
            try {
                httpClient.close();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }
}
