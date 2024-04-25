import Axios from 'axios';

export default class ProductService {
    static apiResourceEndpoint = '/api/product';

    static async getTestProduct() {
        return Axios.get(this.apiResourceEndpoint + '/test');
    }
}
